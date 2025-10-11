<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\TransactionLog;
use App\Models\CurrentStock;
use App\Models\Plant;
use App\Models\Region;
use App\Models\DestinationSale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MaterialController extends Controller
{
    /**
     * Menampilkan daftar stok material untuk fasilitas tertentu.
     */
    public function index(Plant $plant, Request $request)
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

        // Query untuk items yang ada di plant ini
        $query = Item::query()
            ->whereHas('currentStocks', function ($q) use ($plant) {
                $q->where('lokasi_id', $plant->plant_id);
            });

        // Search functionality
        $query->when($filters['search'], function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });

        // Get items dengan relasi
        $items = $query->with(['currentStocks' => function ($q) use ($plant) {
            $q->where('lokasi_id', $plant->plant_id);
        }, 'transactionLogs' => function ($q) use ($plant, $filters) {
            $q->where(function ($subQ) use ($plant) {
                $subQ->where('lokasi_actor_id', $plant->plant_id)
                      ->orWhere('lokasi_tujuan_id', $plant->plant_id);
            });
            if ($filters['start_date']) {
                $q->whereDate('tanggal_transaksi', '>=', $filters['start_date']);
            }
            if ($filters['end_date']) {
                $q->whereDate('tanggal_transaksi', '<=', $filters['end_date']);
            }
        }])->paginate(10)->withQueryString();

        // Prepare data untuk view
        $processedItems = collect();
        foreach ($items as $item) {
            $currentStock = $item->getStockAtLocation($plant->plant_id);

            // Hitung total transaksi berdasarkan filter tanggal
            $penerimaanTotal = $item->transactionLogs()
                ->where('tipe_pergerakan', 'Penerimaan')
                ->where('lokasi_tujuan_id', $plant->plant_id)
                ->when($filters['start_date'], fn($q) => $q->whereDate('tanggal_transaksi', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($q) => $q->whereDate('tanggal_transaksi', '<=', $filters['end_date']))
                ->sum('kuantitas');

            $penyaluranTotal = $item->transactionLogs()
                ->where('tipe_pergerakan', 'Penyaluran')
                ->where('lokasi_actor_id', $plant->plant_id)
                ->when($filters['start_date'], fn($q) => $q->whereDate('tanggal_transaksi', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($q) => $q->whereDate('tanggal_transaksi', '<=', $filters['end_date']))
                ->sum('kuantitas');

            $salesTotal = $item->transactionLogs()
                ->where('tipe_pergerakan', 'Transaksi Sales')
                ->where('lokasi_actor_id', $plant->plant_id)
                ->when($filters['start_date'], fn($q) => $q->whereDate('tanggal_transaksi', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($q) => $q->whereDate('tanggal_transaksi', '<=', $filters['end_date']))
                ->sum('kuantitas');

            $latestTransaction = $item->transactionLogs()
                ->where(function ($q) use ($plant) {
                    $q->where('lokasi_actor_id', $plant->plant_id)
                      ->orWhere('lokasi_tujuan_id', $plant->plant_id);
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

        // Get semua locations untuk dropdown
        $pusatRegion = Region::where('nama_regions', 'P.Layang (Pusat)')->first();
        $allPlants = Plant::with('region')->get();

        $locations = collect();
        if ($pusatRegion) {
            $locations->push(['id' => 'pusat', 'name' => 'P.Layang (Pusat)']);
        }
        foreach ($allPlants as $p) {
            if ($p->plant_id != $plant->plant_id) {
                $locations->push(['id' => $p->plant_id, 'name' => $p->nama_plant]);
            }
        }

        return view('dashboard_page.list_material.data_material', [
            'facility' => $plant,
            'items' => $itemsForView,
            'filters' => $filters,
            'locations' => $locations,
            'pageTitle' => 'Daftar Stok Material - ' . $plant->nama_plant,
            'breadcrumbs' => ['Menu', 'Data Transaksi', 'Daftar Stok Material - ' . $plant->nama_plant],
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        return response()->json([
            'id' => $item->id,
            'nama_material' => $item->nama_material,
            'kode_material' => $item->kode_material,
            'stok_awal' => $item->stok_awal,
            'stok_akhir' => $item->stok_akhir,
        ]);
    }

    /**
     * Memperbarui data material.
     * Tidak ada perubahan logika besar di sini.
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
            $stokAwalBaru = $request->stok_awal;

            // Update hanya stok_awal, stok_akhir tetap tidak berubah
            $item->update([
                'nama_material' => $newName,
                'kode_material' => $newKode,
                'stok_awal' => $stokAwalBaru,
                // stok_akhir tidak diubah agar tetap sesuai dengan transaksi yang terjadi
            ]);

            if ($oldKode !== $newKode) {
                Item::where('kode_material', $oldKode)
                    ->where('id', '!=', $item->id)
                    ->update([
                        'nama_material' => $newName,
                        'kode_material' => $newKode,
                    ]);
            } else {
                Item::where('kode_material', $oldKode)
                    ->where('id', '!=', $item->id)
                    ->update([
                        'nama_material' => $newName,
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
        $facilityId = $item->facility_id;
        $materialName = $item->nama_material;

        try {
            DB::transaction(function () use ($item) {
                // Hapus semua transaksi terkait item ini di fasilitas
                $item->transactions()->delete();

                // Hapus item secara permanen dari database
                $item->delete();
            });
            return redirect()->route('materials.index', $facilityId)->with('success', "Material '{$materialName}' berhasil dihapus secara permanen.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Gagal menghapus material: " . $e->getMessage());
        }
    }

    /**
     * Menangani semua jenis transaksi (penyaluran, penerimaan, sales)
     */
    public function processTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,item_id',
            'jenis_transaksi' => 'required|in:penyaluran,penerimaan,sales',
            'jumlah' => 'required|integer|min:1',
            'tanggal_transaksi' => 'required|date',
            'no_surat_persetujuan' => 'nullable|string',
            'no_ba_serah_terima' => 'nullable|string',
            'asal_id' => 'required_if:jenis_transaksi,penyaluran,penerimaan|string',
            'tujuan_id' => 'required_if:jenis_transaksi,penyaluran,penerimaan|string|different:asal_id',
            'tujuan_sales' => 'required_if:jenis_transaksi,sales|string|in:Vendor UPP,Sales Agen,Sales BPT,Sales SPBE'
        ], [
            'tujuan_id.different' => 'Lokasi Tujuan tidak boleh sama dengan Lokasi Asal.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $response = DB::transaction(function () use ($request) {
                $jenis = $request->jenis_transaksi;
                $jumlah = (int) $request->jumlah;
                $tanggal = Carbon::parse($request->tanggal_transaksi);

                // Get the item
                $item = Item::findOrFail($request->item_id);

                if ($jenis === 'sales') {
                    // Sales dari plant saat ini
                    $destinationSale = DestinationSale::where('nama_tujuan', $request->tujuan_sales)->first();

                    if (!$destinationSale) {
                        // Buat destination baru jika belum ada
                        $destinationSale = DestinationSale::create([
                            'nama_tujuan' => $request->tujuan_sales,
                            'keterangan' => 'Sales destination'
                        ]);
                    }

                    // Cek stok di plant (lokasi actor)
                    // Lokasi actor adalah plant tempat item ini berada
                    $lokasiActorId = null;
                    $currentStock = 0;

                    // Cari lokasi dimana item ini ada stoknya
                    foreach ($item->currentStocks as $stock) {
                        if ($stock->current_quantity > 0) {
                            $lokasiActorId = $stock->lokasi_id;
                            $currentStock = $stock->current_quantity;
                            break;
                        }
                    }

                    if ($lokasiActorId === null || $currentStock < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => "Stok tidak mencukupi! Stok tersedia: {$currentStock} pcs"]);
                    }

                    // Kurangi stok
                    $stokAkhir = $item->decrementStockAtLocation($lokasiActorId, $jumlah);

                    // Catat transaksi
                    TransactionLog::create([
                        'tanggal_transaksi' => $tanggal,
                        'item_id' => $item->item_id,
                        'tipe_pergerakan' => 'Transaksi Sales',
                        'kuantitas' => $jumlah,
                        'stok_akhir_sebelum' => $currentStock,
                        'stok_akhir_sesudah' => $stokAkhir,
                        'lokasi_actor_id' => $lokasiActorId,
                        'destination_sales_id' => $destinationSale->destination_id,
                        'keterangan' => $request->no_surat_persetujuan ? 'No. Surat: ' . $request->no_surat_persetujuan : null,
                    ]);

                    return ['success' => true, 'message' => 'Sales berhasil!'];

                } else {
                    // Penyaluran/Penerimaan antar lokasi
                    $asalIsPusat = $request->asal_id === 'pusat';
                    $tujuanIsPusat = $request->tujuan_id === 'pusat';

                    $pusatRegion = Region::where('nama_regions', 'P.Layang (Pusat)')->first();
                    if (!$pusatRegion) {
                        throw new \Exception('Region P.Layang (Pusat) tidak ditemukan!');
                    }

                    // Tentukan lokasi asal dan tujuan
                    $lokasiAsalId = $asalIsPusat ? $pusatRegion->region_id : $request->asal_id;
                    $lokasiTujuanId = $tujuanIsPusat ? $pusatRegion->region_id : $request->tujuan_id;

                    // Cek stok di lokasi asal
                    $stokAsal = $item->getStockAtLocation($lokasiAsalId);
                    if ($stokAsal < $jumlah) {
                        $namaLokasiAsal = $asalIsPusat ? 'P.Layang (Pusat)' : Plant::find($lokasiAsalId)->nama_plant;
                        throw ValidationException::withMessages(['jumlah' => "Stok di {$namaLokasiAsal} tidak mencukupi! Stok tersedia: {$stokAsal} pcs"]);
                    }

                    // Cek stok di lokasi tujuan
                    $stokTujuan = $item->getStockAtLocation($lokasiTujuanId);

                    // Kurangi stok asal
                    $stokAkhirAsal = $item->decrementStockAtLocation($lokasiAsalId, $jumlah);

                    // Tambah stok tujuan
                    $stokAkhirTujuan = $item->incrementStockAtLocation($lokasiTujuanId, $jumlah);

                    // Tentukan tipe pergerakan
                    $tipePergerakan = ($jenis === 'penyaluran') ? 'Penyaluran' : 'Penerimaan';

                    // Catat transaksi
                    TransactionLog::create([
                        'tanggal_transaksi' => $tanggal,
                        'item_id' => $item->item_id,
                        'tipe_pergerakan' => $tipePergerakan,
                        'kuantitas' => $jumlah,
                        'stok_akhir_sebelum' => $stokAsal,
                        'stok_akhir_sesudah' => $stokAkhirAsal,
                        'lokasi_actor_id' => $lokasiAsalId,
                        'lokasi_tujuan_id' => $lokasiTujuanId,
                        'keterangan' => $request->no_surat_persetujuan ? 'No. Surat: ' . $request->no_surat_persetujuan : null,
                    ]);

                    return ['success' => true, 'message' => 'Transfer berhasil!'];
                }
            });

            return response()->json($response);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Transaction Error: ' . $e->getMessage() . ' line: ' . $e->getLine());
            return response()->json(['message' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API endpoint for DataTables to fetch facility materials data
     */
    public function getFacilityMaterials(Request $request, Plant $facility)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'] ?? '';
        $order = $request->get('order')[0] ?? null;
        $columns = $request->get('columns') ?? [];

        // Build query untuk items yang ada di plant ini
        $query = Item::query()
            ->whereHas('currentStocks', function ($q) use ($facility) {
                $q->where('lokasi_id', $facility->plant_id);
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
            // Get current stock at facility
            $currentStock = $item->getStockAtLocation($facility->plant_id);

            // Calculate totals from transaction logs
            $penerimaanTotal = TransactionLog::where('item_id', $item->item_id)
                ->where('tipe_pergerakan', 'Penerimaan')
                ->where('lokasi_tujuan_id', $facility->plant_id)
                ->sum('kuantitas');

            $penyaluranTotal = TransactionLog::where('item_id', $item->item_id)
                ->where('tipe_pergerakan', 'Penyaluran')
                ->where('lokasi_actor_id', $facility->plant_id)
                ->sum('kuantitas');

            $salesTotal = TransactionLog::where('item_id', $item->item_id)
                ->where('tipe_pergerakan', 'Transaksi Sales')
                ->where('lokasi_actor_id', $facility->plant_id)
                ->sum('kuantitas');

            $latestTransaction = TransactionLog::where('item_id', $item->item_id)
                ->where(function ($q) use ($facility) {
                    $q->where('lokasi_actor_id', $facility->plant_id)
                      ->orWhere('lokasi_tujuan_id', $facility->plant_id);
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
                    . '<button class="btn btn-sm btn-success transaksi-btn" data-item-id="' . $item->item_id . '" title="Transaksi"><i class="fas fa-exchange-alt"></i></button> '
                    . '<form action="' . route('materials.destroy', $item->item_id) . '" method="POST" class="d-inline">'
                    . csrf_field() . method_field('DELETE')
                    . '<button type="submit" class="btn btn-sm btn-danger" title="Hapus"><i class="fas fa-trash"></i></button>'
                    . '</form>'
            ];
        }

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
}