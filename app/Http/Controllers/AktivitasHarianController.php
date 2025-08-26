<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemTransaction;

class AktivitasHarianController extends Controller
{
    public function index()
    {
        // Arahkan ke view utama dari menu aktivitas harian
        return view('dashboard_page.menu.aktivitas_harian');
    }

    public function logTransaksi(Request $request)
    {
        // Ambil semua input filter dari request
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Mulai query dengan memuat semua relasi yang dibutuhkan (eager loading)
        // Ini lebih efisien daripada memuat relasi satu per satu (lazy loading)
        $query = ItemTransaction::with(['item', 'user', 'facilityFrom', 'facilityTo', 'regionFrom', 'regionTo']);

        // Terapkan filter PENCARIAN jika ada input 'search'
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($subQuery) use ($search) {
                // Mencari di kolom pada tabel 'item_transactions'
                $subQuery->orWhere('no_surat_persetujuan', 'like', "%{$search}%")
                    ->orWhere('no_ba_serah_terima', 'like', "%{$search}%");

                // Mencari di tabel relasi 'items'
                $subQuery->orWhereHas('item', function ($itemQuery) use ($search) {
                    $itemQuery->where('nama_material', 'like', "%{$search}%")
                        ->orWhere('kode_material', 'like', "%{$search}%");
                });

                // Mencari di tabel relasi 'users'
                $subQuery->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });

                // Mencari di tabel relasi 'facilities' (sebagai facilityFrom)
                $subQuery->orWhereHas('facilityFrom', function ($facilityQuery) use ($search) {
                    // [PERBAIKAN] Menggunakan kolom 'name' sesuai standar
                    $facilityQuery->where('name', 'like', "%{$search}%");
                });

                // Mencari di tabel relasi 'facilities' (sebagai facilityTo)
                $subQuery->orWhereHas('facilityTo', function ($facilityQuery) use ($search) {
                    // [PERBAIKAN] Menggunakan kolom 'name' sesuai standar
                    $facilityQuery->where('name', 'like', "%{$search}%");
                });

                // Mencari di tabel relasi 'regions' (sebagai regionFrom)
                $subQuery->orWhereHas('regionFrom', function ($regionQuery) use ($search) {
                    // [PERBAIKAN] Menggunakan kolom 'name_region'
                    $regionQuery->where('name_region', 'like', "%{$search}%");
                });

                // Mencari di tabel relasi 'regions' (sebagai regionTo)
                $subQuery->orWhereHas('regionTo', function ($regionQuery) use ($search) {
                    // [PERBAIKAN] Menggunakan kolom 'name_region'
                    $regionQuery->where('name_region', 'like', "%{$search}%");
                });
            });
        });

        // Terapkan filter TANGGAL AWAL jika ada input 'start_date'
        $query->when($startDate, function ($q, $date) {
            return $q->whereDate('created_at', '>=', $date);
        });

        // Terapkan filter TANGGAL AKHIR jika ada input 'end_date'
        $query->when($endDate, function ($q, $date) {
            return $q->whereDate('created_at', '<=', $date);
        });

        // Urutkan hasil berdasarkan tanggal transaksi terbaru
        // Lakukan paginasi dan pastikan parameter filter tetap ada di URL pagination
        $transactions = $query->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        // Kirim data ke view
        return view('dashboard_page.aktivitas_harian.data_transaksi', compact(
            'transactions',
            'search',
            'startDate',
            'endDate'
        ));
    }
}
