<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Carbon\Carbon;

class PusatController extends Controller
{
    /**
     * Menampilkan halaman data P.Layang (Pusat) dengan data yang difilter dan dipaginasi.
     */
    public function index(Request $request)
    {
        // Ambil input dari request untuk filter
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Mulai query untuk model Item, khusus P.Layang (facility_id is NULL)
        $query = Item::whereNull('facility_id')->with('transactions');

        // Terapkan filter pencarian jika ada
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_material', 'like', "%{$search}%")
                    ->orWhere('kode_material', 'like', "%{$search}%");
            });
        }

        // Terapkan filter tanggal jika ada
        // Filter ini bekerja dengan memeriksa apakah item memiliki transaksi dalam rentang tanggal yang diberikan
        if ($startDate) {
            $query->whereHas('transactions', function ($q) use ($startDate) {
                $q->whereDate('created_at', '>=', $startDate);
            });
        }
        if ($endDate) {
            $query->whereHas('transactions', function ($q) use ($endDate) {
                $q->whereDate('created_at', '<=', $endDate);
            });
        }

        // Lakukan paginasi dan tambahkan query string agar filter tetap aktif saat pindah halaman
        $items = $query->latest('updated_at')->paginate(10)->withQueryString();

        // Hitung nilai tambahan untuk setiap item (penerimaan, penyaluran, dll)
        // Ini dilakukan setelah paginasi untuk efisiensi
        $items->getCollection()->transform(function ($item) {
            // Menggunakan relasi 'transactions' yang sudah di-eager load
            $penerimaan = $item->transactions->where('jenis_transaksi', 'penerimaan')->sum('jumlah');
            $penyaluran = $item->transactions->where('jenis_transaksi', 'penyaluran')->sum('jumlah');

            $item->penerimaan_total = $penerimaan;
            $item->penyaluran_total = $penyaluran;
            $item->stok_akhir = $item->stok_awal + $penerimaan - $penyaluran;
            $item->tanggal_transaksi_terakhir = $item->transactions->max('created_at');

            return $item;
        });

        // Kirim data ke view
        return view('dashboard_page.menu.data_pusat', [
            'items' => $items,
            'filters' => $request->only(['search', 'start_date', 'end_date']) // Kirim filter kembali ke view
        ]);
    }
}