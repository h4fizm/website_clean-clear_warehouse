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
use App\Models\Facility;
use App\Models\ItemTransaction;
use Carbon\Carbon;

class PusatController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        $query = Item::query()
            ->whereNull('facility_id') // hanya item pusat
            ->select('items.*');

        // 🔎 Filter pencarian
        $query->when($filters['search'], function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });

        // 📅 Filter item berdasarkan transaksi ATAU updated_at
        $query->when($filters['start_date'] || $filters['end_date'], function ($q) use ($filters) {
            $q->where(function ($sub) use ($filters) {
                $sub->whereHas('transactions', function ($subQ) use ($filters) {
                    if ($filters['start_date']) {
                        $subQ->whereDate('created_at', '>=', $filters['start_date']);
                    }
                    if ($filters['end_date']) {
                        $subQ->whereDate('created_at', '<=', $filters['end_date']);
                    }
                });
                if ($filters['start_date']) {
                    $sub->orWhereDate('items.updated_at', '>=', $filters['start_date']);
                }
                if ($filters['end_date']) {
                    $sub->whereDate('items.updated_at', '<=', $filters['end_date']);
                }
            });
        });

        // ➕ Subquery kalkulasi
        $query->addSelect([
            'penerimaan_total' => ItemTransaction::query()
                ->join('items as source_item', 'item_transactions.item_id', '=', 'source_item.id')
                ->whereColumn('source_item.kode_material', 'items.kode_material')
                ->whereColumn('item_transactions.region_to', 'items.region_id')
                ->when($filters['start_date'], function ($subQ, $date) {
                    $subQ->whereDate('item_transactions.created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($subQ, $date) {
                    $subQ->whereDate('item_transactions.created_at', '<=', $date);
                })
                ->selectRaw('COALESCE(SUM(item_transactions.jumlah), 0)'),
            'penyaluran_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'transfer')
                ->when($filters['start_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '<=', $date);
                }),
            'sales_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'sales')
                ->when($filters['start_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '<=', $date);
                }),
        ]);

        // 📌 Order by latest activity (updated_at ATAU transaksi terakhir)
        $query->orderByDesc(DB::raw("
            GREATEST(
                COALESCE(items.updated_at, '1970-01-01'),
                COALESCE((
                    SELECT MAX(created_at) 
                    FROM item_transactions 
                    WHERE item_transactions.item_id = items.id
                ), '1970-01-01')
            )
        "));

        $items = $query->paginate(10)->withQueryString();
        $facilities = Facility::orderBy('name')->get();

        return view('dashboard_page.menu.data_pusat', [
            'items' => $items,
            'filters' => $filters,
            'facilities' => $facilities,
        ]);
    }

    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id_pusat' => 'required|exists:items,id',
            'kode_material' => 'required|string',
            'jenis_transaksi' => 'required|in:penyaluran,penerimaan,sales',
            'jumlah' => 'required|integer|min:1',
            'tanggal_transaksi' => 'required|date',
            'no_surat_persetujuan' => 'nullable|string',
            'no_ba_serah_terima' => 'nullable|string',
            'facility_id_selected' => 'required_if:jenis_transaksi,penyaluran,penerimaan|exists:facilities,id',
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

                $itemPusat = Item::where('id', $request->item_id_pusat)->lockForUpdate()->firstOrFail();

                // ===============================================
                // CASE 1: SALES TRANSACTION
                // ===============================================
                if ($jenis_transaksi == 'sales') {
                    if ($itemPusat->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages([
                            'jumlah' => 'Stok di gudang pusat tidak mencukupi untuk sales!'
                        ]);
                    }

                    $stokAwalAsal = $itemPusat->stok_akhir;
                    $stokAkhirAsal = $stokAwalAsal - $jumlah; // <-- PERBAIKAN KUNCI

                    // Update stok di tabel items
                    $itemPusat->decrement('stok_akhir', $jumlah);

                    // Buat log transaksi dengan data yang sudah dihitung manual
                    ItemTransaction::create([
                        'item_id' => $itemPusat->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'sales',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAkhirAsal, // <-- Gunakan hasil perhitungan
                        'region_from' => $itemPusat->region_id,
                        'tujuan_sales' => $request->tujuan_sales,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                    ]);

                    return ['success' => true, 'message' => 'Transaksi sales berhasil dicatat!'];
                }

                // ===============================================
                // CASE 2: PENYALURAN (DISTRIBUTION/TRANSFER OUT)
                // ===============================================
                elseif ($jenis_transaksi == 'penyaluran') {
                    if ($itemPusat->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages([
                            'jumlah' => 'Stok di gudang pusat tidak mencukupi untuk penyaluran!'
                        ]);
                    }

                    $itemTujuan = Item::firstOrCreate(
                        ['facility_id' => $request->facility_id_selected, 'kode_material' => $itemPusat->kode_material],
                        ['nama_material' => $itemPusat->nama_material, 'stok_awal' => 0, 'stok_akhir' => 0]
                    );

                    $itemTujuan = Item::where('id', $itemTujuan->id)->lockForUpdate()->first();

                    $stokAwalAsal = $itemPusat->stok_akhir;
                    $stokAwalTujuan = $itemTujuan->stok_akhir;

                    // PERBAIKAN: Hitung manual stok akhir untuk asal dan tujuan
                    $stokAkhirAsal = $stokAwalAsal - $jumlah;
                    $stokAkhirTujuan = $stokAwalTujuan + $jumlah;

                    $itemPusat->decrement('stok_akhir', $jumlah);
                    $itemTujuan->increment('stok_akhir', $jumlah);

                    ItemTransaction::create([
                        'item_id' => $itemPusat->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'transfer',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAkhirAsal, // <-- Gunakan hasil perhitungan
                        'stok_awal_tujuan' => $stokAwalTujuan,
                        'stok_akhir_tujuan' => $stokAkhirTujuan, // <-- Gunakan hasil perhitungan
                        'region_from' => $itemPusat->region_id,
                        'facility_to' => $request->facility_id_selected,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                    ]);

                    return ['success' => true, 'message' => 'Transfer material berhasil dicatat!'];
                }

                // ===============================================
                // CASE 3: PENERIMAAN (RECEPTION/TRANSFER IN)
                // ===============================================
                elseif ($jenis_transaksi == 'penerimaan') {
                    $itemFrom = Item::where('facility_id', $request->facility_id_selected)
                        ->where('kode_material', $request->kode_material)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($itemFrom->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages([
                            'jumlah' => 'Stok di fasilitas asal tidak mencukupi untuk pengiriman!'
                        ]);
                    }

                    $stokAwalAsal = $itemFrom->stok_akhir;
                    $stokAwalTujuan = $itemPusat->stok_akhir;

                    // PERBAIKAN: Hitung manual stok akhir untuk asal dan tujuan
                    $stokAkhirAsal = $stokAwalAsal - $jumlah;
                    $stokAkhirTujuan = $stokAwalTujuan + $jumlah;

                    $itemFrom->decrement('stok_akhir', $jumlah);
                    $itemPusat->increment('stok_akhir', $jumlah);

                    ItemTransaction::create([
                        'item_id' => $itemFrom->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'transfer',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAkhirAsal, // <-- Gunakan hasil perhitungan
                        'stok_awal_tujuan' => $stokAwalTujuan,
                        'stok_akhir_tujuan' => $stokAkhirTujuan, // <-- Gunakan hasil perhitungan
                        'facility_from' => $request->facility_id_selected,
                        'region_to' => $itemPusat->region_id,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                    ]);

                    return ['success' => true, 'message' => 'Penerimaan material berhasil dicatat!'];
                }
            });

            return response()->json($response);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Transfer Error: ' . $e->getMessage());
            return response()->json(['message' => 'Terjadi kesalahan pada server saat memproses transaksi.'], 500);
        }
    }


    public function create()
    {
        return view('dashboard_page.menu.tambah_material');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')],
            'kode_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')],
            'total_stok' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Item::create([
            'nama_material' => $request->nama_material,
            'kode_material' => $request->kode_material,
            'stok_awal' => $request->total_stok,
            'stok_akhir' => $request->total_stok,
            'facility_id' => null,
            'region_id' => 1,
        ]);
        return response()->json(['success' => true, 'message' => 'Data material berhasil ditambahkan!', 'redirect_url' => route('pusat.index')], 201);
    }


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
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_item_id', $item->id);
        }

        // Simpan kode material lama untuk sinkronisasi
        $oldKode = $item->kode_material;

        // Hitung ulang total penerimaan dan penyaluran
        $totalPenerimaan = ItemTransaction::whereHas('item', function ($query) use ($item) {
            $query->where('kode_material', $item->kode_material);
        })
            ->where('region_to', $item->region_id)
            ->sum('jumlah');

        $totalPenyaluran = $item->transactions()
            ->where('jenis_transaksi', 'transfer')
            ->sum('jumlah');

        $stokAwalBaru = $request->stok_awal;

        // ✅ Formula baru: tidak mengurangi stok karena sales
        // ✅ versi baru (tanpa minus sales, karena sales sudah decrement di transfer)
        $stokAkhirBaru = $stokAwalBaru + $totalPenerimaan - $totalPenyaluran;


        // Update stok awal & akhir
        $item->update([
            'stok_awal' => $stokAwalBaru,
            'stok_akhir' => $stokAkhirBaru,
        ]);

        // Sinkronisasi nama & kode material ke semua lokasi
        Item::where('kode_material', $oldKode)
            ->update([
                'nama_material' => $request->input('nama_material'),
                'kode_material' => $request->input('kode_material'),
            ]);

        return redirect()
            ->route('materials.index', $item->facility_id)
            ->with('success', 'Data material berhasil diperbarui!');
    }


    public function destroy(Item $item)
    {
        if ($item->transactions()->exists()) {
            return redirect()->route('pusat.index')->with('error', 'Gagal menghapus! Material ini memiliki riwayat transaksi.');
        }

        $item->delete();
        return redirect()->route('pusat.index')->with('success', 'Data material berhasil dihapus!');
    }

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

        $startDate = $filters['start_date'] ? Carbon::parse($filters['start_date'])->format('d-m-Y') : 'Awal';
        $endDate = $filters['end_date'] ? Carbon::parse($filters['end_date'])->format('d-m-Y') : 'Akhir';
        $filename = "Laporan Data Pusat ({$startDate} - {$endDate}).xlsx";

        return Excel::download(new PusatDataExport($filters), $filename);
    }
}