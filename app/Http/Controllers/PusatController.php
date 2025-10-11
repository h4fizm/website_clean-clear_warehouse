<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PusatDataExport;
use App\Models\Item;
use App\Models\Plant;
use App\Models\Region;
use App\Models\TransactionLog;
use App\Models\CurrentStock;
use App\Models\DestinationSale;
use Carbon\Carbon;

class PusatController extends Controller
{
    /**
     * Menampilkan daftar stok material di gudang pusat.
     */
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        // If no date filter is applied, default to the current month
        if (empty($filters['start_date']) && empty($filters['end_date'])) {
            $filters['start_date'] = Carbon::now()->startOfMonth()->toDateString();
            $filters['end_date'] = Carbon::now()->endOfMonth()->toDateString();
        }

        // Pusat menggunakan lokasi_id = 1 secara langsung
        $pusatLokasiId = 1;

        // Query untuk items yang ada di pusat
        $query = Item::query();

        // Filter berdasarkan item yang memiliki stok di pusat
        $query->whereHas('currentStocks', function ($q) use ($pusatLokasiId) {
            $q->where('lokasi_id', $pusatLokasiId);
        });

        // Search functionality
        $query->when($filters['search'], function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });

        // Get items dengan relasi currentStocks dan transactionLogs
        $items = $query->with(['currentStocks' => function ($q) use ($pusatLokasiId) {
            $q->where('lokasi_id', $pusatLokasiId);
        }, 'transactionLogs' => function ($q) use ($pusatLokasiId, $filters) {
            $q->where(function ($subQ) use ($pusatLokasiId) {
                $subQ->where('lokasi_actor_id', $pusatLokasiId)
                      ->orWhere('lokasi_tujuan_id', $pusatLokasiId);
            });
            if ($filters['start_date']) {
                $q->whereDate('tanggal_transaksi', '>=', $filters['start_date']);
            }
            if ($filters['end_date']) {
                $q->whereDate('tanggal_transaksi', '<=', $filters['end_date']);
            }
        }])->paginate(10)->withQueryString();

        // Get semua facilities/SPBE untuk dropdown
        $plants = Plant::with('region')->get();

        // Prepare data untuk view
        $processedItems = collect();
        foreach ($items as $item) {
            $currentStock = $item->getStockAtLocation($pusatLokasiId);

            // Hitung total transaksi berdasarkan filter tanggal
            $penerimaanTotal = $item->transactionLogs()
                ->where('tipe_pergerakan', 'Penerimaan')
                ->where('lokasi_tujuan_id', $pusatLokasiId)
                ->when($filters['start_date'], fn($q) => $q->whereDate('tanggal_transaksi', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($q) => $q->whereDate('tanggal_transaksi', '<=', $filters['end_date']))
                ->sum('kuantitas');

            $penyaluranTotal = $item->transactionLogs()
                ->where('tipe_pergerakan', 'Penyaluran')
                ->where('lokasi_actor_id', $pusatLokasiId)
                ->when($filters['start_date'], fn($q) => $q->whereDate('tanggal_transaksi', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($q) => $q->whereDate('tanggal_transaksi', '<=', $filters['end_date']))
                ->sum('kuantitas');

            $salesTotal = $item->transactionLogs()
                ->where('tipe_pergerakan', 'Transaksi Sales')
                ->where('lokasi_actor_id', $pusatLokasiId)
                ->when($filters['start_date'], fn($q) => $q->whereDate('tanggal_transaksi', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($q) => $q->whereDate('tanggal_transaksi', '<=', $filters['end_date']))
                ->sum('kuantitas');

            $latestTransaction = $item->transactionLogs()
                ->where(function ($q) use ($pusatLokasiId) {
                    $q->where('lokasi_actor_id', $pusatLokasiId)
                      ->orWhere('lokasi_tujuan_id', $pusatLokasiId);
                })
                ->orderBy('tanggal_transaksi', 'desc')
                ->first();

            $processedItems->push([
                'item_id' => $item->item_id,
                'nama_material' => $item->nama_material,
                'kode_material' => $item->kode_material,
                'kategori_material' => $item->kategori_material,
                'stok_awal' => 0, // TODO: Hitung dari initial stock jika ada
                'stok_akhir' => $currentStock,
                'penerimaan_total' => $penerimaanTotal,
                'penyaluran_total' => $penyaluranTotal,
                'sales_total' => $salesTotal,
                'latest_transaction_date' => $latestTransaction ? $latestTransaction->tanggal_transaksi : $item->updated_at,
                'item' => $item // untuk keperluan API
            ]);
        }

        // Convert ke LengthAwarePaginator
        $currentPage = $items->currentPage();
        $perPage = $items->perPage();
        $total = $processedItems->count();
        $itemsForView = new \Illuminate\Pagination\LengthAwarePaginator(
            $processedItems->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('dashboard_page.menu.data_pusat', [
            'items' => $itemsForView,
            'filters' => $filters,
            'facilities' => $plants,
            'pusatLokasiId' => $pusatLokasiId
        ]);
    }

    /**
     * Metode untuk melakukan transaksi transfer, penerimaan, atau sales.
     * Tidak ada perubahan logika besar yang diperlukan di sini terkait bug, tetapi menambahkan 'is_active'.
     */
    /**
     * Metode untuk melakukan transaksi transfer, penerimaan, atau sales.
     */
    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id_pusat' => 'required_if:jenis_transaksi,penyaluran,sales|exists:items,item_id',
            'kode_material' => 'required|string',
            'jenis_transaksi' => 'required|in:penyaluran,penerimaan,sales',
            'jumlah' => 'required|integer|min:1',
            'tanggal_transaksi' => 'required|date',
            'no_surat_persetujuan' => 'nullable|string',
            'no_ba_serah_terima' => 'nullable|string',
            'facility_id_selected' => 'required_if:jenis_transaksi,penyaluran,penerimaan|exists:plants,plant_id',
            'tujuan_sales' => 'required_if:jenis_transaksi,sales|string|in:Vendor UPP,Sales Agen,Sales BPT,Sales SPBE'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $response = DB::transaction(function () use ($request) {
                $jenis_transaksi = $request->jenis_transaksi;
                $jumlah = (int) $request->jumlah;
                $tanggal_transaksi = Carbon::parse($request->tanggal_transaksi);

                // Pusat menggunakan lokasi_id = 1 secara langsung
                $pusatLokasiId = 1;

                // Cari item berdasarkan kode material
                $item = Item::where('kode_material', $request->kode_material)->firstOrFail();

                if ($jenis_transaksi == 'penyaluran') {
                    // Transfer dari Pusat ke Plant
                    $plantTujuan = Plant::findOrFail($request->facility_id_selected);

                    // Cek stok di pusat
                    $stokPusat = $item->getStockAtLocation($pusatLokasiId);
                    if ($stokPusat < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => "Stok di gudang pusat tidak mencukupi! Stok tersedia: {$stokPusat} pcs"]);
                    }

                    // Cek stok di tujuan
                    $stokTujuan = $item->getStockAtLocation($plantTujuan->plant_id);

                    // Kurangi stok pusat
                    $stokAkhirPusat = $item->decrementStockAtLocation($pusatLokasiId, $jumlah);

                    // Tambah stok tujuan
                    $stokAkhirTujuan = $item->incrementStockAtLocation($plantTujuan->plant_id, $jumlah);

                    // Catat transaksi
                    TransactionLog::create([
                        'tanggal_transaksi' => $tanggal_transaksi,
                        'item_id' => $item->item_id,
                        'tipe_pergerakan' => 'Penyaluran',
                        'kuantitas' => $jumlah,
                        'stok_akhir_sebelum' => $stokPusat,
                        'stok_akhir_sesudah' => $stokAkhirPusat,
                        'lokasi_actor_id' => $pusatLokasiId,
                        'lokasi_tujuan_id' => $plantTujuan->plant_id,
                        'keterangan' => $request->no_surat_persetujuan ? 'No. Surat: ' . $request->no_surat_persetujuan : null,
                    ]);

                    return ['success' => true, 'message' => 'Penyaluran material dari pusat berhasil!'];

                } elseif ($jenis_transaksi == 'penerimaan') {
                    // Transfer dari Plant ke Pusat
                    $plantAsal = Plant::findOrFail($request->facility_id_selected);

                    // Cek stok di plant asal
                    $stokAsal = $item->getStockAtLocation($plantAsal->plant_id);
                    if ($stokAsal < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => "Stok di {$plantAsal->nama_plant} tidak mencukupi! Stok tersedia: {$stokAsal} pcs"]);
                    }

                    // Cek stok di pusat
                    $stokPusat = $item->getStockAtLocation($pusatLokasiId);

                    // Kurangi stok asal
                    $stokAkhirAsal = $item->decrementStockAtLocation($plantAsal->plant_id, $jumlah);

                    // Tambah stok pusat
                    $stokAkhirPusat = $item->incrementStockAtLocation($pusatLokasiId, $jumlah);

                    // Catat transaksi
                    TransactionLog::create([
                        'tanggal_transaksi' => $tanggal_transaksi,
                        'item_id' => $item->item_id,
                        'tipe_pergerakan' => 'Penerimaan',
                        'kuantitas' => $jumlah,
                        'stok_akhir_sebelum' => $stokAsal,
                        'stok_akhir_sesudah' => $stokAkhirAsal,
                        'lokasi_actor_id' => $plantAsal->plant_id,
                        'lokasi_tujuan_id' => $pusatLokasiId,
                        'keterangan' => $request->no_surat_persetujuan ? 'No. Surat: ' . $request->no_surat_persetujuan : null,
                    ]);

                    return ['success' => true, 'message' => 'Penerimaan material ke pusat berhasil!'];

                } elseif ($jenis_transaksi == 'sales') {
                    // Sales dari Pusat
                    $destinationSale = DestinationSale::where('nama_tujuan', $request->tujuan_sales)->first();

                    if (!$destinationSale) {
                        // Buat destination baru jika belum ada
                        $destinationSale = DestinationSale::create([
                            'nama_tujuan' => $request->tujuan_sales,
                            'keterangan' => 'Sales destination'
                        ]);
                    }

                    // Cek stok di pusat
                    $stokPusat = $item->getStockAtLocation($pusatLokasiId);
                    if ($stokPusat < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => "Stok di gudang pusat tidak mencukupi! Stok tersedia: {$stokPusat} pcs"]);
                    }

                    // Kurangi stok pusat
                    $stokAkhirPusat = $item->decrementStockAtLocation($pusatLokasiId, $jumlah);

                    // Catat transaksi
                    TransactionLog::create([
                        'tanggal_transaksi' => $tanggal_transaksi,
                        'item_id' => $item->item_id,
                        'tipe_pergerakan' => 'Transaksi Sales',
                        'kuantitas' => $jumlah,
                        'stok_akhir_sebelum' => $stokPusat,
                        'stok_akhir_sesudah' => $stokAkhirPusat,
                        'lokasi_actor_id' => $pusatLokasiId,
                        'destination_sales_id' => $destinationSale->destination_id,
                        'keterangan' => $request->no_surat_persetujuan ? 'No. Surat: ' . $request->no_surat_persetujuan : null,
                    ]);

                    return ['success' => true, 'message' => 'Transaksi sales dari pusat berhasil!'];
                }
            });

            return response()->json($response);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Transfer Error: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json(['message' => 'Terjadi kesalahan pada server: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Menambahkan material baru di gudang pusat.
     * Tidak ada perubahan logika besar yang diperlukan di sini terkait bug, tetapi menambahkan 'is_active'.
     */
    public function create()
    {
        return view('dashboard_page.menu.tambah_material');
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(Item $item)
    {
        return view('dashboard_page.menu.tambah_material', compact('item'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')],
            'kode_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')],
            'stok_awal' => 'required|integer|min:0',
            'kategori_material' => ['required', 'string', 'in:Baru,Baik,Rusak,Afkir'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Item::create([
            'nama_material' => $request->nama_material,
            'kode_material' => $request->kode_material,
            'kategori_material' => $request->kategori_material,
            'stok_awal' => $request->stok_awal,
            'stok_akhir' => $request->stok_awal,
            'facility_id' => null,
            'region_id' => 1,
            'is_active' => true // ✅ PERBAIKAN: Set is_active saat membuat item baru
        ]);

        return response()->json(['success' => true, 'message' => 'Data material berhasil ditambahkan!', 'redirect_url' => route('pusat.index')], 201);
    }

    /**
     * Memperbarui data material.
     * Tidak ada perubahan logika besar yang diperlukan di sini terkait bug.
     */
    public function update(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'nama_material' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items')->where('facility_id', $item->facility_id)->ignore($item->id),
            ],
            'kode_material' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items')->where('facility_id', $item->facility_id)->ignore($item->id),
            ],
            'stok_awal' => 'required|integer|min:0',
            'kategori_material' => ['required', 'string', 'in:Baru,Baik,Rusak,Afkir'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::transaction(function () use ($request, $item) {
            $oldKode = $item->kode_material;
            $newKode = $request->input('kode_material');
            $newName = $request->input('nama_material');
            $newKategori = $request->input('kategori_material');

            $stokAwalBaru = $request->stok_awal;

            // Update hanya stok_awal, stok_akhir tetap tidak berubah
            $item->update([
                'nama_material' => $newName,
                'kode_material' => $newKode,
                'kategori_material' => $newKategori,
                'stok_awal' => $stokAwalBaru,
                // stok_akhir tidak diubah agar tetap sesuai dengan transaksi yang terjadi
            ]);

            if ($oldKode !== $newKode || $item->wasChanged('nama_material') || $item->wasChanged('kategori_material')) {
                Item::where('kode_material', $oldKode)
                    ->where('id', '!=', $item->id)
                    ->update([
                        'nama_material' => $newName,
                        'kode_material' => $newKode,
                        'kategori_material' => $newKategori,
                    ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Data material berhasil diperbarui! Hanya stok awal yang diubah.'
        ]);
    }

    /**
     * Menghapus material secara permanen (hard delete) dari database.
     * Material dapat dihapus meskipun memiliki stok.
     */
    public function destroy(Item $item)
    {
        $materialName = $item->nama_material;

        try {
            DB::transaction(function () use ($item) {
                // Hapus semua transaksi terkait item ini di pusat
                $item->transactions()->delete();

                // Hapus item secara permanen dari database
                $item->delete();
            });
            return redirect()->route('pusat.index')->with('success', "Material '{$materialName}' berhasil dihapus secara permanen.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Gagal menghapus material: " . $e->getMessage());
        }
    }

    /**
     * Mengekspor data material ke Excel.
     * Tidak ada perubahan logika di sini.
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        $today = Carbon::now()->isoFormat('dddd, D MMMM YYYY');
        $startDate = $filters['start_date'] ? Carbon::parse($filters['start_date'])->format('d-m-Y') : 'Awal';
        $endDate = $filters['end_date'] ? Carbon::parse($filters['end_date'])->format('d-m-Y') : 'Akhir';

        $filename = "Laporan Data Pusat ({$startDate} - {$endDate}).xlsx";

        return Excel::download(new PusatDataExport($filters), $filename);
    }

    /**
     * API endpoint for DataTables to fetch Pusat materials data
     */
    public function getPusatMaterials(Request $request)
    {
        // Validate and sanitize input parameters
        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search')['value'] ?? '';
        $order = $request->get('order')[0] ?? null;
        $columns = $request->get('columns') ?? [];

        // Ensure valid values
        $start = max(0, $start);
        $length = max(1, min(100, $length));

        try {
            // Pusat menggunakan lokasi_id = 1 secara langsung
            $pusatLokasiId = 1;

            // Build query untuk items yang ada di pusat
            $query = Item::query()
                ->whereHas('currentStocks', function ($q) use ($pusatLokasiId) {
                    $q->where('lokasi_id', $pusatLokasiId);
                });

            // Add search functionality
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('nama_material', 'like', "%{$search}%")
                      ->orWhere('kode_material', 'like', "%{$search}%");
                });
            }

            // Get total records count
            $totalRecords = $query->count();

            // Add ordering
            if ($order) {
                $columnIndex = $order['column'];
                $direction = $order['dir'];
                $columnName = $columns[$columnIndex]['data'] ?? 'item_id';

                switch ($columnName) {
                    case 'kode_material':
                        $query->orderBy('kode_material', $direction);
                        break;
                    case 'nama_material':
                        $query->orderBy('nama_material', $direction);
                        break;
                    case 'created_at':
                        $query->orderBy('updated_at', $direction);
                        break;
                    default:
                        $query->orderBy('updated_at', 'desc');
                        break;
                }
            } else {
                $query->orderBy('updated_at', 'desc');
            }

            // Get the filtered records count before pagination
            $filteredQuery = clone $query;
            $filteredRecords = $filteredQuery->count();

            // Add pagination
            if ($length > 0) {
                $query->skip($start)->take($length);
            }

            // Get the items
            $items = $query->get();

            $data = [];
            foreach ($items as $item) {
                // Get current stock at pusat
                $currentStock = $item->getStockAtLocation($pusatLokasiId);

                // Calculate totals from transaction logs
                $penerimaanTotal = TransactionLog::where('item_id', $item->item_id)
                    ->where('tipe_pergerakan', 'Penerimaan')
                    ->where('lokasi_tujuan_id', $pusatLokasiId)
                    ->sum('kuantitas');

                $penyaluranTotal = TransactionLog::where('item_id', $item->item_id)
                    ->where('tipe_pergerakan', 'Penyaluran')
                    ->where('lokasi_actor_id', $pusatLokasiId)
                    ->sum('kuantitas');

                $salesTotal = TransactionLog::where('item_id', $item->item_id)
                    ->where('tipe_pergerakan', 'Transaksi Sales')
                    ->where('lokasi_actor_id', $pusatLokasiId)
                    ->sum('kuantitas');

                $latestTransaction = TransactionLog::where('item_id', $item->item_id)
                    ->where(function ($q) use ($pusatLokasiId) {
                        $q->where('lokasi_actor_id', $pusatLokasiId)
                          ->orWhere('lokasi_tujuan_id', $pusatLokasiId);
                    })
                    ->orderBy('tanggal_transaksi', 'desc')
                    ->first();

                $data[] = [
                    'id' => $item->item_id,
                    'kode_material' => $item->kode_material,
                    'nama_material' => $item->nama_material,
                    'kategori_material' => $item->kategori_material,
                    'stok_awal' => 0, // TODO: Hitung dari initial stock
                    'penerimaan_total' => (int) $penerimaanTotal,
                    'penyaluran_total' => (int) $penyaluranTotal,
                    'sales_total' => (int) $salesTotal,
                    'pemusnahan_total' => 0, // Tidak ada pemusnahan untuk sementara
                    'stok_akhir' => (int) $currentStock,
                    'created_at' => $latestTransaction ? $latestTransaction->tanggal_transaksi->format('Y-m-d H:i:s') : $item->updated_at->format('Y-m-d H:i:s'),
                    'actions' => '<button class="btn btn-sm btn-warning edit-btn" data-item-id="' . $item->item_id . '" title="Edit"><i class="fas fa-pencil-alt"></i></button> '
                        . '<button class="btn btn-sm btn-success kirim-btn" data-item-id="' . $item->item_id . '" title="Kirim"><i class="fas fa-paper-plane"></i></button> '
                        . '<form action="' . route('pusat.destroy', $item->item_id) . '" method="POST" class="d-inline">'
                        . csrf_field() . method_field('DELETE')
                        . '<button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>'
                        . '</form>'
                ];
            }

            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $filteredRecords,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Pusat API Error: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}