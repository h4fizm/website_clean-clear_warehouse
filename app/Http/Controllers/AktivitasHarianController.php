<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemTransaction;
// Tidak perlu use Facility lagi karena sudah tidak digunakan di sini

class AktivitasHarianController extends Controller
{
    public function index()
    {
        return view('dashboard_page.menu.aktivitas_harian');
    }

    public function logTransaksi(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Memuat semua relasi yang dibutuhkan untuk tampilan
        $query = ItemTransaction::with(['item', 'user', 'facilityFrom', 'facilityTo', 'regionFrom', 'regionTo']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                // Mencari di tabel utama
                $q->orWhere('no_surat_persetujuan', 'like', "%{$search}%")
                    ->orWhere('no_ba_serah_terima', 'like', "%{$search}%");

                // Mencari di semua tabel relasi
                $q->orWhereHas('item', function ($subQuery) use ($search) {
                    $subQuery->where('nama_material', 'like', "%{$search}%")
                        ->orWhere('kode_material', 'like', "%{$search}%");
                });
                $q->orWhereHas('user', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%");
                });
                $q->orWhereHas('facilityFrom', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%"); // Perbaikan: nama_facility -> name
                });
                $q->orWhereHas('facilityTo', function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%"); // Perbaikan: nama_facility -> name
                });
                $q->orWhereHas('regionFrom', function ($subQuery) use ($search) {
                    $subQuery->where('name_region', 'like', "%{$search}%"); // Perbaikan: nama_region -> name_region
                });
                $q->orWhereHas('regionTo', function ($subQuery) use ($search) {
                    $subQuery->where('name_region', 'like', "%{$search}%"); // Perbaikan: nama_region -> name_region
                });
            });
        }

        if ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        $transactions = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Variabel yang tidak perlu (locations dan locationPov) sudah dihapus
        return view('dashboard_page.aktivitas_harian.data_transaksi', compact(
            'transactions',
            'search',
            'startDate',
            'endDate'
        ));
    }
}