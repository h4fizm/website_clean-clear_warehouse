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

// Tambahkan use statement untuk AktivitasHarianController
use App\Http\Controllers\AktivitasHarianController;

class PusatController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        // Memilih kolom items.* secara eksplisit dan menambahkan kolom-kolom kalkulasi
        $query = Item::query()
            ->whereNull('facility_id')
            ->select('items.*');

        $query->when($filters['search'], function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });

        // Filter berdasarkan tanggal transaksi atau updated_at
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

        // Subquery untuk kalkulasi
        $query->addSelect([
            'penerimaan_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'penerimaan')
                ->when($filters['start_date'], fn($subQ) => $subQ->whereDate('created_at', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($subQ) => $subQ->whereDate('created_at', '<=', $filters['end_date'])),

            'penyaluran_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'penyaluran')
                ->when($filters['start_date'], fn($subQ) => $subQ->whereDate('created_at', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($subQ) => $subQ->whereDate('created_at', '<=', $filters['end_date'])),

            'sales_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'sales')
                ->when($filters['start_date'], fn($subQ) => $subQ->whereDate('created_at', '>=', $filters['start_date']))
                ->when($filters['end_date'], fn($subQ) => $subQ->whereDate('created_at', '<=', $filters['end_date'])),

            'pemusnahan_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
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
            'item_id_pusat' => 'nullable|exists:items,id',
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
                $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->first();

                if (!$pusatRegion) {
                    return ['success' => false, 'message' => 'Region Pusat tidak ditemukan di database.'];
                }

                $aktivitasController = new AktivitasHarianController();

                if ($jenis_transaksi == 'penyaluran') {
                    $itemPusat = Item::where('id', $request->item_id_pusat)->lockForUpdate()->firstOrFail();
                    $stokAkhirDinamis = $itemPusat->stok_awal +
                        $itemPusat->transactions()->where('jenis_transaksi', 'penerimaan')->sum('jumlah') -
                        $itemPusat->transactions()->where('jenis_transaksi', 'penyaluran')->sum('jumlah') -
                        $itemPusat->transactions()->where('jenis_transaksi', 'sales')->sum('jumlah') -
                        $itemPusat->transactions()->where('jenis_transaksi', 'pemusnahan')->where('status', 'done')->sum('jumlah');

                    if ($stokAkhirDinamis < $jumlah) {
                        throw ValidationException::withMessages([
                            'jumlah' => "Stok di gudang pusat tidak mencukupi untuk penyaluran! Stok saat ini: {$stokAkhirDinamis}"
                        ]);
                    }

                    $facilityTujuan = Facility::where('id', $request->facility_id_selected)->first();
                    $itemTujuan = Item::firstOrCreate(
                        ['facility_id' => $facilityTujuan->id, 'kode_material' => $itemPusat->kode_material],
                        ['nama_material' => $itemPusat->nama_material, 'stok_awal' => 0, 'stok_akhir' => 0, 'region_id' => $facilityTujuan->region_id, 'kategori_material' => $itemPusat->kategori_material]
                    );
                    $itemTujuan = Item::where('id', $itemTujuan->id)->lockForUpdate()->first();

                    $stokAwalTujuan = $itemTujuan->stok_akhir;

                    $itemPusat->update(['stok_akhir' => $stokAkhirDinamis - $jumlah]);
                    $itemTujuan->increment('stok_akhir', $jumlah);

                    // Siapkan data untuk log transaksi pengeluaran dari pusat
                    $logDataPenyaluran = new Request([
                        'item_id' => $itemPusat->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'penyaluran',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAkhirDinamis,
                        'stok_akhir_asal' => $stokAkhirDinamis - $jumlah,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                        'region_from' => $itemPusat->region_id,
                        'facility_to' => $request->facility_id_selected,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                    ]);
                    $aktivitasController->logTransaksi($logDataPenyaluran);

                    // Siapkan data untuk log transaksi penerimaan di fasilitas tujuan
                    $logDataPenerimaan = new Request([
                        'item_id' => $itemTujuan->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'penerimaan',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalTujuan,
                        'stok_akhir_asal' => $stokAwalTujuan + $jumlah,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                        'facility_from' => null, // Asal dari pusat
                        'region_to' => $itemTujuan->region_id,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                    ]);
                    $aktivitasController->logTransaksi($logDataPenerimaan);

                    return ['success' => true, 'message' => 'Penyaluran material berhasil dicatat!'];
                } elseif ($jenis_transaksi == 'penerimaan') {
                    $itemFrom = Item::where('facility_id', $request->facility_id_selected)
                        ->where('kode_material', $request->kode_material)
                        ->lockForUpdate()
                        ->first();

                    if (!$itemFrom) {
                        throw ValidationException::withMessages([
                            'facility_id_selected' => 'Kode material tidak ditemukan di fasilitas asal.'
                        ]);
                    }

                    if ($itemFrom->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages([
                            'jumlah' => 'Stok di fasilitas asal tidak mencukupi untuk pengiriman! Saat ini: ' . $itemFrom->stok_akhir
                        ]);
                    }

                    $itemPusat = Item::firstOrCreate(
                        ['facility_id' => null, 'kode_material' => $request->kode_material],
                        [
                            'nama_material' => $itemFrom->nama_material,
                            'stok_awal' => 0,
                            'stok_akhir' => 0,
                            'region_id' => $pusatRegion->id,
                            'kategori_material' => $itemFrom->kategori_material
                        ]
                    );

                    $stokAwalAsal = $itemFrom->stok_akhir;
                    $stokAwalTujuan = $itemPusat->stok_akhir;

                    $itemFrom->decrement('stok_akhir', $jumlah);
                    $itemPusat->increment('stok_akhir', $jumlah);

                    // Siapkan data untuk log transaksi pengeluaran di fasilitas asal
                    $logDataPenyaluran = new Request([
                        'item_id' => $itemFrom->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'penyaluran',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAwalAsal - $jumlah,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                        'facility_from' => $request->facility_id_selected,
                        'region_from' => $itemFrom->region_id,
                        'facility_to' => null, // Tujuan ke pusat
                        'region_to' => $pusatRegion->id,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                    ]);
                    $aktivitasController->logTransaksi($logDataPenyaluran);

                    // Siapkan data untuk log transaksi penerimaan di pusat
                    $logDataPenerimaan = new Request([
                        'item_id' => $itemPusat->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'penerimaan',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalTujuan,
                        'stok_akhir_asal' => $stokAwalTujuan + $jumlah,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                        'facility_from' => $request->facility_id_selected,
                        'region_to' => $pusatRegion->id,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                    ]);
                    $aktivitasController->logTransaksi($logDataPenerimaan);

                    return ['success' => true, 'message' => 'Penerimaan material berhasil dicatat!'];
                } elseif ($jenis_transaksi == 'sales') {
                    $itemPusat = Item::where('id', $request->item_id_pusat)->lockForUpdate()->firstOrFail();
                    $stokAkhirDinamis = $itemPusat->stok_awal +
                        $itemPusat->transactions()->where('jenis_transaksi', 'penerimaan')->sum('jumlah') -
                        $itemPusat->transactions()->where('jenis_transaksi', 'penyaluran')->sum('jumlah') -
                        $itemPusat->transactions()->where('jenis_transaksi', 'sales')->sum('jumlah') -
                        $itemPusat->transactions()->where('jenis_transaksi', 'pemusnahan')->where('status', 'done')->sum('jumlah');

                    if ($stokAkhirDinamis < $jumlah) {
                        throw ValidationException::withMessages([
                            'jumlah' => "Stok di gudang pusat tidak mencukupi untuk sales! Stok saat ini: {$stokAkhirDinamis}"
                        ]);
                    }

                    $stokAwalAsal = $stokAkhirDinamis;
                    $stokAkhirAsal = $stokAwalAsal - $jumlah;

                    $itemPusat->update(['stok_akhir' => $stokAkhirDinamis - $jumlah]);

                    // Siapkan data untuk log transaksi sales
                    $logDataSales = new Request([
                        'item_id' => $itemPusat->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'sales',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAkhirAsal,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                        'region_from' => $itemPusat->region_id,
                        'tujuan_sales' => $request->tujuan_sales,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                    ]);
                    $aktivitasController->logTransaksi($logDataSales);

                    return ['success' => true, 'message' => 'Transaksi sales berhasil dicatat!'];
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

        // Ambil semua transaksi yang relevan untuk item ini
        $allTransactions = ItemTransaction::where('item_id', $item->id)->get();
        $totalPenerimaan = $allTransactions->where('jenis_transaksi', 'penerimaan')->sum('jumlah');
        $totalPenyaluran = $allTransactions->where('jenis_transaksi', 'penyaluran')->sum('jumlah');
        $totalSales = $allTransactions->where('jenis_transaksi', 'sales')->sum('jumlah');
        $totalPemusnahan = $allTransactions->where('jenis_transaksi', 'pemusnahan')->where('status', 'done')->sum('jumlah');

        $stokAwalBaru = $request->stok_awal;
        $stokAkhirBaru = $stokAwalBaru + $totalPenerimaan - $totalPenyaluran - $totalSales - $totalPemusnahan;

        $item->update([
            'stok_awal' => $stokAwalBaru,
            'stok_akhir' => $stokAkhirBaru,
            'nama_material' => $request->input('nama_material'),
            'kode_material' => $request->input('kode_material'),
        ]);

        return redirect()
            ->route('pusat.index')
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

        $today = Carbon::now()->isoFormat('dddd, D MMMM YYYY');
        $startDate = $filters['start_date'] ? Carbon::parse($filters['start_date'])->format('d-m-Y') : 'Awal';
        $endDate = $filters['end_date'] ? Carbon::parse($filters['end_date'])->format('d-m-Y') : 'Akhir';

        $filename = "Laporan Data Pusat ({$startDate} - {$endDate}).xlsx";

        return Excel::download(new PusatDataExport($filters), $filename);
    }
}