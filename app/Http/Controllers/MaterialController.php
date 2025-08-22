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

        // 3. Terapkan filter pencarian berdasarkan nama atau kode material dengan GROUPING
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

        // 7. Kirim semua data yang diperlukan ke view
        return view('dashboard_page.list_material.data_material', [
            'facility' => $facility,
            'items' => $items,
            'filters' => $filters,
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
                // Pastikan unik hanya untuk facility ini, abaikan item saat ini
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
                // Kirim ID item yang error agar modal yang benar terbuka kembali
                ->with('error_item_id', $item->id);
        }

        $item->update($request->only(['nama_material', 'kode_material', 'stok_awal']));

        return redirect()->route('materials.index', $item->facility_id)->with('success', 'Data material berhasil diperbarui!');
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
}