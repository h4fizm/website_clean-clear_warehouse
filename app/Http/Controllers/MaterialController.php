<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemTransaction;
use App\Models\Facility;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
// PENTING: Tambahkan use statement untuk AktivitasHarianController
use App\Http\Controllers\AktivitasHarianController;

class MaterialController extends Controller
{
    public function index(Facility $facility, Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        $itemsQuery = $facility->items()->select('items.*');

        // Filter pencarian (search)
        $itemsQuery->when($filters['search'], function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });

        // Filter daftar item utama berdasarkan rentang tanggal transaksi.
        $itemsQuery->when($filters['start_date'] || $filters['end_date'], function ($query) use ($filters) {
            $query->where(function ($sub) use ($filters) {
                $sub->whereHas('transactions', function ($subQuery) use ($filters) {
                    if ($filters['start_date']) {
                        $subQuery->whereDate('created_at', '>=', $filters['start_date']);
                    }
                    if ($filters['end_date']) {
                        $subQuery->whereDate('created_at', '<=', $filters['end_date']);
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

        // Perbaikan: Subquery untuk kalkulasi total transaksi
        $itemsQuery->addSelect([
            'penerimaan_total' => ItemTransaction::query()
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'penerimaan')
                ->when($filters['start_date'], function ($query, $date) {
                    $query->whereDate('created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($query, $date) {
                    $query->whereDate('created_at', '<=', $date);
                })
                ->selectRaw('COALESCE(SUM(jumlah), 0)'),

            'penyaluran_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'penyaluran')
                ->when($filters['start_date'], function ($query, $date) {
                    $query->whereDate('created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($query, $date) {
                    $query->whereDate('created_at', '<=', $date);
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

            'pemusnahan_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'pemusnahan')
                ->where('status', 'done')
                ->when($filters['start_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($subQ, $date) {
                    $subQ->whereDate('created_at', '<=', $date);
                }),

            'latest_transaction_date' => ItemTransaction::selectRaw('MAX(created_at)')
                ->whereColumn('item_id', 'items.id'),
        ]);


        $itemsQuery->orderByDesc(DB::raw("
        GREATEST(
            COALESCE(items.updated_at, '1970-01-01'),
            COALESCE((
                SELECT MAX(created_at)
                FROM item_transactions
                WHERE item_transactions.item_id = items.id
            ), '1970-01-01')
        )
    "));

        $items = $itemsQuery->paginate(10)->withQueryString();
        $allFacilities = Facility::orderBy('name')->get(['id', 'name']);
        $allRegions = Region::orderBy('name_region')->get(['id', 'name_region']);

        $locations = collect([['id' => 'pusat', 'name' => 'P.Layang (Pusat)']]);
        $allFacilities->each(function ($fac) use (&$locations) {
            $locations->push(['id' => (string) $fac->id, 'name' => $fac->name]);
        });

        return view('dashboard_page.list_material.data_material', [
            'facility' => $facility,
            'items' => $items,
            'filters' => $filters,
            'locations' => $locations,
            'allFacilities' => $allFacilities,
            'allRegions' => $allRegions,
            'pageTitle' => 'Daftar Stok Material - ' . $facility->name,
            'breadcrumbs' => ['Menu', 'Data Transaksi', 'Daftar Stok Material - ' . $facility->name],
        ]);
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
            ->route('materials.index', $item->facility_id)
            ->with('success', 'Data material berhasil diperbarui!');
    }

    public function destroy(Item $item)
    {
        $facilityId = $item->facility_id;
        if ($item->transactions()->exists()) {
            return redirect()->route('materials.index', $facilityId)->with('error', 'Gagal menghapus! Material ini memiliki riwayat transaksi.');
        }

        $item->delete();
        return redirect()->route('materials.index', $facilityId)->with('success', 'Data material berhasil dihapus!');
    }

    public function processTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'kode_material' => 'required|string',
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
                $jenis_transaksi = $request->jenis_transaksi;
                $jumlah = (int) $request->jumlah;
                $tanggal_transaksi = Carbon::parse($request->tanggal_transaksi);
                $kodeMaterial = $request->kode_material;

                // Menginisialisasi controller aktivitas harian
                $aktivitasController = new AktivitasHarianController();

                // --- CASE 1: SALES ---
                if ($jenis_transaksi == 'sales') {
                    $itemAsal = Item::where('id', $request->item_id)->lockForUpdate()->firstOrFail();
                    if ($itemAsal->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => 'Stok di ' . $itemAsal->facility->name . ' tidak mencukupi untuk sales!']);
                    }
                    $stokAwalAsal = $itemAsal->stok_akhir;
                    $stokAkhirAsal = $stokAwalAsal - $jumlah;
                    $itemAsal->decrement('stok_akhir', $jumlah);

                    $logData = new Request([
                        'item_id' => $itemAsal->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'sales',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAkhirAsal,
                        'tanggal_transaksi' => $tanggal_transaksi,
                        'facility_from' => $itemAsal->facility_id,
                        'region_from' => $itemAsal->region_id,
                        'tujuan_sales' => $request->tujuan_sales,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                    ]);
                    $aktivitasController->logTransaksi($logData);

                    return ['success' => true, 'message' => 'Transaksi sales berhasil dicatat!'];
                }

                // --- CASE 2: PENYALURAN & PENERIMAAN ---
                if ($jenis_transaksi == 'penyaluran' || $jenis_transaksi == 'penerimaan') {
                    $asalIsPusat = $request->asal_id == 'pusat';
                    $tujuanIsPusat = $request->tujuan_id == 'pusat';

                    $itemAsal = Item::where('id', $request->item_id)->lockForUpdate()->firstOrFail();
                    $lokasiAsal = $asalIsPusat ? 'Pusat' : $itemAsal->facility->name;

                    if ($itemAsal->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => "Stok di {$lokasiAsal} tidak mencukupi untuk transfer!"]);
                    }

                    $itemTujuan = null;
                    $stokAwalTujuan = 0;
                    $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->first();

                    if ($tujuanIsPusat) {
                        $itemTujuan = Item::whereNull('facility_id')->where('kode_material', $kodeMaterial)->lockForUpdate()->first();
                        if (!$itemTujuan) {
                            $itemTujuan = Item::create([
                                'kode_material' => $kodeMaterial,
                                'nama_material' => $itemAsal->nama_material,
                                'stok_awal' => 0,
                                'stok_akhir' => 0,
                                'region_id' => $pusatRegion ? $pusatRegion->id : null,
                                'facility_id' => null,
                                'kategori_material' => $itemAsal->kategori_material
                            ]);
                        }
                        $stokAwalTujuan = $itemTujuan->stok_akhir;

                    } else {
                        $itemTujuan = Item::firstOrCreate(
                            ['facility_id' => $request->tujuan_id, 'kode_material' => $kodeMaterial],
                            ['nama_material' => $itemAsal->nama_material, 'stok_awal' => 0, 'stok_akhir' => 0, 'region_id' => Facility::find($request->tujuan_id)->region_id ?? null, 'kategori_material' => $itemAsal->kategori_material]
                        );
                        $itemTujuan = Item::where('id', $itemTujuan->id)->lockForUpdate()->first();
                        $stokAwalTujuan = $itemTujuan->stok_akhir;
                    }

                    $stokAwalAsal = $itemAsal->stok_akhir;
                    $stokAkhirAsal = $stokAwalAsal - $jumlah;
                    $stokAkhirTujuan = $stokAwalTujuan + $jumlah;

                    // Update stok di kedua lokasi
                    $itemAsal->decrement('stok_akhir', $jumlah);
                    $itemTujuan->increment('stok_akhir', $jumlah);

                    // Log transaksi untuk lokasi asal (penyaluran/pengeluaran)
                    $logDataAsal = new Request([
                        'item_id' => $itemAsal->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'penyaluran',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAkhirAsal,
                        'tanggal_transaksi' => $tanggal_transaksi,
                        'facility_from' => $itemAsal->facility_id,
                        'region_from' => $itemAsal->region_id,
                        'facility_to' => $itemTujuan->facility_id,
                        'region_to' => $itemTujuan->region_id,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                    ]);
                    $aktivitasController->logTransaksi($logDataAsal);

                    // Log transaksi untuk lokasi tujuan (penerimaan/pemasukan)
                    $logDataTujuan = new Request([
                        'item_id' => $itemTujuan->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'penerimaan',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalTujuan,
                        'stok_akhir_asal' => $stokAkhirTujuan,
                        'tanggal_transaksi' => $tanggal_transaksi,
                        'facility_from' => $itemAsal->facility_id,
                        'region_from' => $itemAsal->region_id,
                        'facility_to' => $itemTujuan->facility_id,
                        'region_to' => $itemTujuan->region_id,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                    ]);
                    $aktivitasController->logTransaksi($logDataTujuan);

                    return ['success' => true, 'message' => "Transfer material berhasil dicatat!"];
                }
            });

            return response()->json($response);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Material Controller Transfer Error: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json(['message' => 'Terjadi kesalahan pada server saat memproses transaksi.'], 500);
        }
    }
}