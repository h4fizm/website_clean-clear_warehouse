<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Item; // Pastikan model Item di-import
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    /**
     * Menampilkan daftar material untuk sebuah facility yang spesifik.
     * Ditambah logika untuk mengirim daftar semua lokasi untuk dropdown transaksi.
     */
    public function index(Facility $facility, Request $request)
    {
        // 1. Ambil semua parameter filter dari request
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        // 2. Mulai query untuk mengambil item dari facility yang dipilih
        $itemsQuery = $facility->items();

        // 3. Terapkan filter pencarian berdasarkan nama atau kode material
        $itemsQuery->when($filters['search'], function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });

        // 4. Terapkan filter tanggal berdasarkan tanggal transaksi
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

        // 5. Hitung total penerimaan, penyaluran, dan ambil tanggal transaksi terakhir
        $itemsQuery->withSum([
            'transactions as penerimaan_total' => function ($q) {
                $q->where('jenis_transaksi', 'penerimaan');
            }
        ], 'jumlah');

        $itemsQuery->withSum([
            'transactions as penyaluran_total' => function ($q) {
                $q->where('jenis_transaksi', 'penyaluran');
            }
        ], 'jumlah');

        $itemsQuery->withMax('transactions as latest_transaction_date', 'created_at');

        // 6. Eksekusi query dengan urutan dan pagination
        $items = $itemsQuery->latest('updated_at')->paginate(10);

        // 7. [BARU] Siapkan data semua lokasi untuk dropdown di modal transaksi
        $allFacilities = Facility::orderBy('name')->get(['id', 'name']);

        // Gabungkan P.Layang dan semua facility menjadi satu koleksi
        $locations = collect([
            ['id' => 'pusat', 'name' => 'P.Layang (Pusat)']
        ]);
        foreach ($allFacilities as $fac) {
            $locations->push(['id' => $fac->id, 'name' => $fac->name]);
        }

        // 8. Kirim data ke view
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
    // [TAMBAHKAN METHOD UPDATE DI SINI]
    /**
     * Memperbarui data material.
     */
    public function update(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'nama_material' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items')
                    ->where('facility_id', $item->facility_id)
                    ->ignore($item->id),
            ],
            'kode_material' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items')
                    ->where('facility_id', $item->facility_id)
                    ->ignore($item->id),
            ],
            'stok_awal' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_item_id', $item->id);
        }

        // simpan kode lama dulu sebelum update
        $oldKode = $item->kode_material;

        // update item cabang
        $item->update($request->only(['nama_material', 'kode_material', 'stok_awal']));

        // sinkronkan perubahan ke pusat
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


    // [TAMBAHKAN METHOD DESTROY DI SINI]
    /**
     * Menghapus data material.
     */
    public function destroy(Item $item)
    {
        // Simpan facility_id untuk redirect sebelum item dihapus/diubah
        $facilityId = $item->facility_id;

        try {
            // Gunakan DB Transaction untuk memastikan semua proses berhasil
            DB::transaction(function () use ($item) {
                // 1. Hapus semua transaksi yang terkait dengan item ini
                $item->transactions()->delete();

                // 2. Atur stok awal item ini menjadi 0
                $item->update(['stok_awal' => 0]);
            });

            return redirect()->route('materials.index', $facilityId)->with('success', 'Stok material berhasil di-reset menjadi 0.');

        } catch (\Exception $e) {
            // Jika terjadi error, kembalikan dengan pesan kesalahan
            return redirect()->route('materials.index', $facilityId)->with('error', 'Terjadi kesalahan saat mereset stok material.');
        }
    }

    /**
     * [MODIFIKASI]
     * Disesuaikan untuk menerima kembali 'jenis_transaksi' dari radio button.
     */
    public function processTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            // Tambahkan kembali validasi untuk jenis_transaksi
            'jenis_transaksi' => 'required|in:penerimaan,penyaluran',
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

        // --- Sisa dari method ini TIDAK ADA PERUBAHAN ---
        // Logika selanjutnya sudah benar dan tidak perlu diubah.
        try {
            $result = DB::transaction(function () use ($request) {
                $kodeMaterial = Item::findOrFail($request->item_id)->kode_material;
                $jumlah = (int) $request->jumlah;
                $itemAsal = null;
                $itemTujuan = null;
                $asalName = '';
                $tujuanName = '';

                if ($request->asal_id == 'pusat') {
                    $itemAsal = Item::whereNull('facility_id')
                        ->where('kode_material', $kodeMaterial)
                        ->first();
                    $asalName = 'P.Layang (Pusat)';
                } else {
                    $itemAsal = Item::where('facility_id', $request->asal_id)->where('kode_material', $kodeMaterial)->first();
                    $asalName = Facility::find($request->asal_id)->name;
                }

                if (!$itemAsal)
                    return ['success' => false, 'message' => "Material tidak ditemukan di lokasi asal ({$asalName})."];
                if ($itemAsal->stok_akhir < $jumlah)
                    return ['success' => false, 'message' => "Stok di {$asalName} tidak mencukupi. Stok saat ini: " . $itemAsal->stok_akhir . " pcs."];

                if ($request->tujuan_id == 'pusat') {
                    $itemTujuan = Item::whereNull('facility_id')
                        ->where('kode_material', $kodeMaterial)
                        ->first();
                    $tujuanName = 'P.Layang (Pusat)';
                    if (!$itemTujuan)
                        return ['success' => false, 'message' => 'Material ini belum terdaftar di P.Layang (Pusat).'];
                } else {
                    $tujuanFacility = Facility::find($request->tujuan_id);
                    $tujuanName = $tujuanFacility->name;
                    $itemTujuan = Item::where('facility_id', $request->tujuan_id)->where('kode_material', $kodeMaterial)->first();
                    if (!$itemTujuan) {
                        $itemTujuan = Item::create([
                            'facility_id' => $request->tujuan_id,
                            'nama_material' => $itemAsal->nama_material,
                            'kode_material' => $kodeMaterial,
                            'stok_awal' => 0,
                        ]);
                    }
                }

                $dataTransaksi = [
                    'jumlah' => $jumlah,
                    'no_surat_persetujuan' => $request->no_surat_persetujuan,
                    'no_ba_serah_terima' => $request->no_ba_serah_terima,
                    'created_at' => $request->tanggal_transaksi . ' ' . now()->toTimeString(),
                    'updated_at' => $request->tanggal_transaksi . ' ' . now()->toTimeString(),
                ];

                $itemAsal->transactions()->create(array_merge($dataTransaksi, ['jenis_transaksi' => 'penyaluran']));
                $itemTujuan->transactions()->create(array_merge($dataTransaksi, ['jenis_transaksi' => 'penerimaan']));
                $formattedDate = \Carbon\Carbon::parse($request->tanggal_transaksi)->locale('id')->translatedFormat('l, d F Y');
                return ['success' => true, 'message' => "Transaksi {$jumlah} pcs dari {$asalName} ke {$tujuanName} pada {$formattedDate} berhasil."];
            });
            return response()->json($result, $result['success'] ? 200 : 400);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan server: ' . $e->getMessage()], 500);
        }
    }
}