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
use App\Models\Region;

class PusatController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->first();

        $query = Item::query()
            ->whereNull('facility_id') // Hanya ambil item yang ada di gudang pusat
            ->select('items.*');

        $query->when($filters['search'], function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });

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

        // ✅ PERBAIKAN: Subquery untuk pemusnahan, hanya mengambil yang berstatus 'done'
        $query->addSelect([
            'penerimaan_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->join('items as source_item', 'item_transactions.item_id', '=', 'source_item.id')
                ->whereColumn('source_item.kode_material', 'items.kode_material')
                ->whereColumn('item_transactions.region_to', 'items.region_id')
                ->where('jenis_transaksi', 'transfer')
                ->when($filters['start_date'], fn($subQ, $date) => $subQ->whereDate('item_transactions.created_at', '>=', $date))
                ->when($filters['end_date'], fn($subQ, $date) => $subQ->whereDate('item_transactions.created_at', '<=', $date)),

            'penyaluran_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->where('jenis_transaksi', 'transfer')
                ->whereColumn('item_id', 'items.id')
                ->when($filters['start_date'], fn($subQ) => $subQ->whereDate('created_at', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($subQ) => $subQ->whereDate('created_at', '<=', $filters['end_date'])),

            'sales_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'sales')
                ->when($filters['start_date'], fn($subQ) => $subQ->whereDate('created_at', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($subQ) => $subQ->whereDate('created_at', '<=', $filters['end_date'])),

            'pemusnahan_total_proses' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'pemusnahan')
                ->where('status', 'proses')
                ->when($filters['start_date'], fn($subQ) => $subQ->whereDate('created_at', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($subQ) => $subQ->whereDate('created_at', '<=', $filters['end_date'])),

            'pemusnahan_total_done' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'pemusnahan')
                ->where('status', 'done')
                ->when($filters['start_date'], fn($subQ) => $subQ->whereDate('created_at', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($subQ) => $subQ->whereDate('created_at', '<=', $filters['end_date'])),

            'latest_transaction_date' => ItemTransaction::selectRaw('MAX(created_at)')
                ->whereColumn('item_id', 'items.id'),
        ]);

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
            'item_id_pusat' => 'required_if:jenis_transaksi,penyaluran,sales|exists:items,id',
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
                $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();

                if ($jenis_transaksi == 'penyaluran' || $jenis_transaksi == 'sales') {
                    $itemPusat = Item::where('id', $request->item_id_pusat)
                        ->lockForUpdate()
                        ->firstOrFail();
                } else {
                    $itemPusat = Item::where('kode_material', $request->kode_material)
                        ->whereNull('facility_id')
                        ->lockForUpdate()
                        ->firstOrFail();
                }

                if ($jenis_transaksi == 'penyaluran') {
                    if ($itemPusat->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => "Stok di gudang pusat tidak mencukupi untuk penyaluran!"]);
                    }

                    $facilityTujuan = Facility::findOrFail($request->facility_id_selected);

                    $itemTujuan = Item::firstOrCreate(
                        ['facility_id' => $facilityTujuan->id, 'kode_material' => $itemPusat->kode_material],
                        [
                            'nama_material' => $itemPusat->nama_material,
                            'stok_awal' => 0,
                            'stok_akhir' => 0,
                            'region_id' => $facilityTujuan->region_id,
                            'kategori_material' => $itemPusat->kategori_material
                        ]
                    );

                    $itemTujuan->lockForUpdate();

                    $stokAwalPusat = $itemPusat->stok_akhir;
                    $stokAwalTujuan = $itemTujuan->stok_akhir;

                    $itemPusat->decrement('stok_akhir', $jumlah);
                    $itemTujuan->increment('stok_akhir', $jumlah);

                    ItemTransaction::create([
                        'item_id' => $itemPusat->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'transfer',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalPusat,
                        'stok_akhir_asal' => $stokAwalPusat - $jumlah,
                        'stok_awal_tujuan' => $stokAwalTujuan,
                        'stok_akhir_tujuan' => $stokAwalTujuan + $jumlah,
                        'facility_from' => null,
                        'region_from' => $pusatRegion->id,
                        'facility_to' => $facilityTujuan->id,
                        'region_to' => $facilityTujuan->region_id,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                    ]);

                    return ['success' => true, 'message' => 'Penyaluran material dari pusat berhasil dicatat!'];

                } elseif ($jenis_transaksi == 'penerimaan') {
                    $itemAsal = Item::where('facility_id', $request->facility_id_selected)
                        ->where('kode_material', $itemPusat->kode_material)
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($itemAsal->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => "Stok di fasilitas asal tidak mencukupi untuk pengiriman!"]);
                    }

                    $stokAwalAsal = $itemAsal->stok_akhir;
                    $stokAwalPusat = $itemPusat->stok_akhir;

                    $itemAsal->decrement('stok_akhir', $jumlah);
                    $itemPusat->increment('stok_akhir', $jumlah);

                    ItemTransaction::create([
                        'item_id' => $itemAsal->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'transfer',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAwalAsal - $jumlah,
                        'stok_awal_tujuan' => $stokAwalPusat,
                        'stok_akhir_tujuan' => $stokAwalPusat + $jumlah,
                        'facility_from' => $request->facility_id_selected,
                        'region_from' => $itemAsal->region_id,
                        'facility_to' => null,
                        'region_to' => $pusatRegion->id,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                    ]);

                    return ['success' => true, 'message' => 'Penerimaan material ke pusat berhasil dicatat!'];

                } elseif ($jenis_transaksi == 'sales') {
                    if ($itemPusat->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => "Stok di gudang pusat tidak mencukupi untuk sales!"]);
                    }

                    $stokAwalAsal = $itemPusat->stok_akhir;

                    $itemPusat->decrement('stok_akhir', $jumlah);

                    ItemTransaction::create([
                        'item_id' => $itemPusat->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'sales',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAwalAsal - $jumlah,
                        'region_from' => $pusatRegion->id,
                        'tujuan_sales' => $request->tujuan_sales,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                    ]);

                    return ['success' => true, 'message' => 'Transaksi sales dari pusat berhasil dicatat!'];
                }
            });

            return response()->json($response);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Transfer Error: ' . $e->getMessage() . ' on line ' . $e->getLine());
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
            'kategori_material' => ['required', 'string', 'in:Baru,Baik,Rusak,Afkir'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Item::create([
            'nama_material' => $request->nama_material,
            'kode_material' => $request->kode_material,
            'kategori_material' => $request->kategori_material,
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
            'kategori_material' => ['required', 'string', 'in:Baru,Baik,Rusak,Afkir'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_item_id', $item->id);
        }

        DB::transaction(function () use ($request, $item) {
            $oldKode = $item->kode_material;
            $newKode = $request->input('kode_material');
            $newName = $request->input('nama_material');
            $newKategori = $request->input('kategori_material');

            // Hitung total penerimaan berdasarkan KODE MATERIAL LAMA
            $totalPenerimaan = ItemTransaction::where('region_to', $item->region_id)
                ->where(function ($query) use ($oldKode) {
                    $query->whereHas('item', function ($q) use ($oldKode) {
                        $q->where('kode_material', $oldKode);
                    });
                })
                ->sum('jumlah');

            // Hitung total penyaluran, sales, dan pemusnahan (DONE) berdasarkan ITEM ID saat ini
            $totalPenyaluran = ItemTransaction::where('item_id', $item->id)
                ->where('jenis_transaksi', 'transfer')
                ->sum('jumlah');

            $totalSales = ItemTransaction::where('item_id', $item->id)
                ->where('jenis_transaksi', 'sales')
                ->sum('jumlah');

            $totalPemusnahan = ItemTransaction::where('item_id', $item->id)
                ->where('jenis_transaksi', 'pemusnahan')
                ->where('status', 'done')
                ->sum('jumlah');

            $stokAwalBaru = $request->stok_awal;
            $stokAkhirBaru = $stokAwalBaru + $totalPenerimaan - $totalPenyaluran - $totalSales - $totalPemusnahan;

            // Perbarui item saat ini dengan data dari request
            $item->update([
                'nama_material' => $newName,
                'kode_material' => $newKode,
                'kategori_material' => $newKategori,
                'stok_awal' => $stokAwalBaru,
                'stok_akhir' => $stokAkhirBaru,
            ]);

            // Jika kode, nama, atau kategori material berubah, perbarui juga item lain yang terkait (di fasilitas)
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

        return redirect()->route('pusat.index')->with('success', 'Data material berhasil diperbarui!');
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

        $today = Carbon::now()->isoFormat('dddd, D MMMM YYYY');
        $startDate = $filters['start_date'] ? Carbon::parse($filters['start_date'])->format('d-m-Y') : 'Awal';
        $endDate = $filters['end_date'] ? Carbon::parse($filters['end_date'])->format('d-m-Y') : 'Akhir';

        $filename = "Laporan Data Pusat ({$startDate} - {$endDate}).xlsx";

        return Excel::download(new PusatDataExport($filters), $filename);
    }
}