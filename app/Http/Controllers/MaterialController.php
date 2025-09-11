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

        $itemsQuery->when($filters['search'], function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });

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

        $itemsQuery->addSelect([
            'penerimaan_total' => ItemTransaction::query()
                ->join('items as source_item', 'item_transactions.item_id', '=', 'source_item.id')
                ->whereColumn('source_item.kode_material', 'items.kode_material')
                ->whereColumn('item_transactions.facility_to', 'items.facility_id')
                ->when($filters['start_date'], function ($query, $date) {
                    $query->whereDate('item_transactions.created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($query, $date) {
                    $query->whereDate('item_transactions.created_at', '<=', $date);
                })
                ->selectRaw('COALESCE(SUM(item_transactions.jumlah), 0)'),
            'penyaluran_total' => ItemTransaction::selectRaw('COALESCE(sum(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'transfer')
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
        ]);

        $itemsQuery->orderByDesc(DB::raw("
            GREATEST(
                COALESCE(items.updated_at, '1970-01-01'),
                COALESCE((
                    SELECT MAX(it.created_at)
                    FROM item_transactions AS it
                    LEFT JOIN items AS source_item ON it.item_id = source_item.id
                    WHERE
                        it.item_id = items.id
                        OR
                        (it.facility_to = items.facility_id AND source_item.kode_material = items.kode_material)
                ), '1970-01-01')
            )
        "));

        $items = $itemsQuery->paginate(10)->withQueryString();
        $allFacilities = Facility::orderBy('name')->get(['id', 'name']);
        $locations = collect([['id' => 'pusat', 'name' => 'P.Layang (Pusat)']]);
        foreach ($allFacilities as $fac) {
            $locations->push(['id' => $fac->id, 'name' => $fac->name]);
        }

        return view('dashboard_page.list_material.data_material', [
            'facility' => $facility,
            'items' => $items,
            'filters' => $filters,
            'locations' => $locations,
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

        $oldKode = $item->kode_material;

        $totalPenerimaan = ItemTransaction::whereHas('item', function ($query) use ($item) {
            $query->where('kode_material', $item->kode_material);
        })
            ->where('facility_to', $item->facility_id)
            ->sum('jumlah');

        $totalPenyaluran = $item->transactions()->where('jenis_transaksi', 'transfer')->sum('jumlah');
        $totalSales = $item->transactions()->where('jenis_transaksi', 'sales')->sum('jumlah');

        $stokAwalBaru = $request->stok_awal;
        $stokAkhirBaru = $stokAwalBaru + $totalPenerimaan - $totalPenyaluran - $totalSales;

        $item->update([
            'stok_awal' => $stokAwalBaru,
            'stok_akhir' => $stokAkhirBaru,
        ]);

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

                if ($jenis_transaksi == 'sales') {
                    $itemFacility = Item::where('id', $request->item_id)->lockForUpdate()->firstOrFail();
                    if ($itemFacility->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => 'Stok di ' . $itemFacility->facility->name . ' tidak mencukupi untuk sales!']);
                    }
                    $stokAwalAsal = $itemFacility->stok_akhir;
                    $stokAkhirAsal = $stokAwalAsal - $jumlah;
                    $itemFacility->decrement('stok_akhir', $jumlah);
                    ItemTransaction::create([
                        'item_id' => $itemFacility->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'sales',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAkhirAsal,
                        'facility_from' => $itemFacility->facility_id,
                        'tujuan_sales' => $request->tujuan_sales,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                    ]);
                    return ['success' => true, 'message' => 'Transaksi sales berhasil dicatat!'];
                }

                if ($jenis_transaksi == 'penyaluran' || $jenis_transaksi == 'penerimaan') {
                    $asalIsPusat = $request->asal_id == 'pusat';
                    $tujuanIsPusat = $request->tujuan_id == 'pusat';

                    $itemAsal = null;
                    if ($asalIsPusat) {
                        $itemAsal = Item::whereNull('facility_id')->where('kode_material', $kodeMaterial)->lockForUpdate()->firstOrFail();
                    } else {
                        $itemAsal = Item::where('facility_id', $request->asal_id)->where('kode_material', $kodeMaterial)->lockForUpdate()->firstOrFail();
                    }

                    if ($itemAsal->stok_akhir < $jumlah) {
                        $namaLokasiAsal = $asalIsPusat ? 'Gudang Pusat' : $itemAsal->facility->name;
                        throw ValidationException::withMessages(['jumlah' => "Stok di {$namaLokasiAsal} tidak mencukupi!"]);
                    }

                    $itemTujuan = null;
                    if ($tujuanIsPusat) {
                        $itemTujuan = Item::whereNull('facility_id')->where('kode_material', $kodeMaterial)->lockForUpdate()->firstOrFail();
                    } else {
                        // ✅ Perbaikan: Tambahkan 'kategori_material' saat membuat item baru
                        $itemTujuan = Item::firstOrCreate(
                            ['facility_id' => $request->tujuan_id, 'kode_material' => $kodeMaterial],
                            ['nama_material' => $itemAsal->nama_material, 'stok_awal' => 0, 'stok_akhir' => 0, 'kategori_material' => $itemAsal->kategori_material]
                        );
                        $itemTujuan = Item::where('id', $itemTujuan->id)->lockForUpdate()->first();
                    }

                    $stokAwalAsal = $itemAsal->stok_akhir;
                    $stokAwalTujuan = $itemTujuan->stok_akhir;
                    $stokAkhirAsal = $stokAwalAsal - $jumlah;
                    $stokAkhirTujuan = $stokAwalTujuan + $jumlah;

                    $itemAsal->decrement('stok_akhir', $jumlah);
                    $itemTujuan->increment('stok_akhir', $jumlah);

                    ItemTransaction::create([
                        'item_id' => $itemAsal->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'transfer',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAkhirAsal,
                        'stok_awal_tujuan' => $stokAwalTujuan,
                        'stok_akhir_tujuan' => $stokAkhirTujuan,
                        'facility_from' => $asalIsPusat ? null : $request->asal_id,
                        'region_from' => $asalIsPusat ? $itemAsal->region_id : null,
                        'facility_to' => $tujuanIsPusat ? null : $request->tujuan_id,
                        'region_to' => $tujuanIsPusat ? $itemTujuan->region_id : null,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => $tanggal_transaksi,
                        'updated_at' => $tanggal_transaksi,
                    ]);

                    return ['success' => true, 'message' => 'Transfer material berhasil dicatat!'];
                }
            });

            return response()->json($response);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Facility Transfer Error: ' . $e->getMessage() . ' on line ' . $e->getLine());
            return response()->json(['message' => 'Terjadi kesalahan pada server saat memproses transaksi.'], 500);
        }
    }
}