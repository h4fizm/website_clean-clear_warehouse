<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemTransaction;

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

        // PENTING: Muat SEMUA relasi yang dibutuhkan untuk tampilan
        $query = ItemTransaction::with(['item', 'user', 'facilityFrom', 'facilityTo', 'regionFrom', 'regionTo']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                // Mencari di tabel utama
                $q->orWhere('jenis_transaksi', 'like', "%{$search}%")
                    ->orWhere('no_surat_persetujuan', 'like', "%{$search}%")
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
                    $subQuery->where('nama_facility', 'like', "%{$search}%");
                });
                $q->orWhereHas('facilityTo', function ($subQuery) use ($search) {
                    $subQuery->where('nama_facility', 'like', "%{$search}%");
                });
                $q->orWhereHas('regionFrom', function ($subQuery) use ($search) {
                    $subQuery->where('nama_region', 'like', "%{$search}%");
                });
                $q->orWhereHas('regionTo', function ($subQuery) use ($search) {
                    $subQuery->where('nama_region', 'like', "%{$search}%");
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

        return view('dashboard_page.aktivitas_harian.data_transaksi', compact(
            'transactions',
            'search',
            'startDate',
            'endDate'
        ));
    }
}