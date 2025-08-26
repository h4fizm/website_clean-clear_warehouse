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

class MaterialController extends Controller
{
    // app/Http/Controllers/MaterialController.php

    public function index(Facility $facility, Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        // [FIX] Panggil select() di awal untuk menetapkan kolom dasar
        $itemsQuery = $facility->items()->select('items.*');

        // Filter (tidak ada perubahan)
        $itemsQuery->when($filters['search'], function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });
        $itemsQuery->when($filters['start_date'], function ($query, $date) {
            return $query->whereHas('transactions', function ($subQuery) use ($date) {
                $subQuery->whereDate('created_at', '>=', $date);
            });
        });
        $itemsQuery->when($filters['end_date'], function ($query, $date) {
            return $query->whereHas('transactions', function ($subQuery) use ($date) {
                $subQuery->whereDate('created_at', '<=', $date);
            });
        });

        // =====================================================================
        // =================== INI BAGIAN YANG DIPERBAIKI ======================
        // =====================================================================

        // [FIX] Gunakan addSelect() SETELAH select() dan pakai COALESCE untuk kedua perhitungan
        $itemsQuery->addSelect([
            // Subquery untuk PENERIMAAN (barang masuk ke facility ini)
            'penerimaan_total' => ItemTransaction::selectRaw('COALESCE(sum(jumlah), 0)')
                ->whereColumn('facility_to', 'items.facility_id')
                ->whereHas('item', function ($query) {
                    $query->whereColumn('kode_material', 'items.kode_material');
                }),

            // Subquery untuk PENYALURAN (barang keluar dari item ini)
            'penyaluran_total' => ItemTransaction::selectRaw('COALESCE(sum(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
        ]);

        // =====================================================================
        // =================== AKHIR BAGIAN PERBAIKAN ==========================
        // =====================================================================

        $itemsQuery->withMax('transactions as latest_transaction_date', 'created_at');

        $items = $itemsQuery->latest('updated_at')->paginate(10);

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
            'breadcrumbs' => [
                'Menu',
                'Data Transaksi',
                'Daftar Stok Material - ' . $facility->name,
            ],
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
        $item->update($request->only(['nama_material', 'kode_material', 'stok_awal']));
        Item::whereNull('facility_id')
            ->where('kode_material', $oldKode)
            ->update([
                'nama_material' => $item->nama_material,
                'kode_material' => $item->kode_material,
            ]);

        return redirect()
            ->route('materials.index', $item->facility_id)
            ->with('success', 'Data material berhasil diperbarui!');
    }

    public function destroy(Item $item)
    {
        $facilityId = $item->facility_id;
        try {
            DB::transaction(function () use ($item) {
                $item->transactions()->delete();
                $item->update(['stok_awal' => 0]);
            });
            return redirect()->route('materials.index', $facilityId)->with('success', 'Stok material berhasil di-reset menjadi 0.');
        } catch (\Exception $e) {
            return redirect()->route('materials.index', $facilityId)->with('error', 'Terjadi kesalahan saat mereset stok material.');
        }
    }

    public function processTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'asal_id' => 'required|string',
            'tujuan_id' => 'required|string|different:asal_id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_transaksi' => 'required|date',
            'no_surat_persetujuan' => 'nullable|string|max:255',
            'no_ba_serah_terima' => 'nullable|string|max:255',
        ], [
            'tujuan_id.different' => 'Lokasi Tujuan tidak boleh sama dengan Lokasi Asal.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $result = DB::transaction(function () use ($request) {
                $kodeMaterial = Item::findOrFail($request->item_id)->kode_material;
                $jumlah = (int) $request->jumlah;

                // ... (logika menentukan itemAsal tetap sama)
                $itemAsal = null;
                $asalName = '';
                $asalIsPusat = $request->asal_id == 'pusat';

                if ($asalIsPusat) {
                    $itemAsal = Item::whereNull('facility_id')->where('kode_material', $kodeMaterial)->firstOrFail();
                    $asalName = Region::find($itemAsal->region_id)->name_region ?? 'P.Layang (Pusat)';
                } else {
                    $itemAsal = Item::where('facility_id', $request->asal_id)->where('kode_material', $kodeMaterial)->firstOrFail();
                    $asalName = Facility::find($request->asal_id)->name;
                }


                if ($itemAsal->stok_akhir < $jumlah) {
                    return ['success' => false, 'message' => "Stok di {$asalName} tidak mencukupi. Stok saat ini: " . $itemAsal->stok_akhir . " pcs."];
                }

                // [BARU] Ambil snapshot stok sebelum transaksi
                $stokAwalAsal = $itemAsal->stok_akhir;
                $stokAkhirAsal = $stokAwalAsal - $jumlah;

                // ... (logika menentukan itemTujuan tetap sama)
                $itemTujuan = null;
                $tujuanName = '';
                $tujuanIsPusat = $request->tujuan_id == 'pusat';

                if ($tujuanIsPusat) {
                    $itemTujuan = Item::whereNull('facility_id')->where('kode_material', $kodeMaterial)->firstOrFail();
                    $tujuanName = Region::find($itemTujuan->region_id)->name_region ?? 'P.Layang (Pusat)';
                } else {
                    $tujuanFacility = Facility::find($request->tujuan_id);
                    $tujuanName = $tujuanFacility->name;
                    $itemTujuan = Item::firstOrCreate(
                        ['facility_id' => $request->tujuan_id, 'kode_material' => $kodeMaterial],
                        ['nama_material' => $itemAsal->nama_material, 'stok_awal' => 0]
                    );
                }

                ItemTransaction::create([
                    'item_id' => $itemAsal->id,
                    'user_id' => Auth::id(),
                    'jenis_transaksi' => 'transfer',
                    'jumlah' => $jumlah,
                    'stok_awal_asal' => $stokAwalAsal,   // <-- SIMPAN
                    'stok_akhir_asal' => $stokAkhirAsal,  // <-- SIMPAN
                    'facility_from' => $asalIsPusat ? null : $request->asal_id,
                    'region_from' => $asalIsPusat ? $itemAsal->region_id : null,
                    'facility_to' => $tujuanIsPusat ? null : $request->tujuan_id,
                    'region_to' => $tujuanIsPusat ? $itemTujuan->region_id : null,
                    'no_surat_persetujuan' => $request->no_surat_persetujuan,
                    'no_ba_serah_terima' => $request->no_ba_serah_terima,
                    'created_at' => $request->tanggal_transaksi . ' ' . now()->toTimeString(),
                    'updated_at' => $request->tanggal_transaksi . ' ' . now()->toTimeString(),
                ]);

                $formattedDate = Carbon::parse($request->tanggal_transaksi)->locale('id')->translatedFormat('l, d F Y');
                return ['success' => true, 'message' => "Transfer {$jumlah} pcs dari {$asalName} ke {$tujuanName} pada {$formattedDate} berhasil."];
            });
            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['success' => false, 'message' => 'Material tidak ditemukan di salah satu lokasi.'], 404);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
        }
    }
}