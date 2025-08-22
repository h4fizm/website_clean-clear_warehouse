<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Item; // Pastikan model Item di-import
use Illuminate\Http\Request;

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
}