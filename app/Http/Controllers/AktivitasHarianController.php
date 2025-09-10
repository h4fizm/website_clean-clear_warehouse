<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemTransaction;
use App\Exports\TransaksiLogExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AktivitasHarianController extends Controller
{
    public function index()
    {
        return view('dashboard_page.aktivitas_harian.data_transaksi');
    }

    public function logTransaksi(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $jenisTransaksi = $request->input('jenis_transaksi');

        $query = ItemTransaction::with(['item', 'user', 'facilityFrom', 'facilityTo', 'regionFrom', 'regionTo']);

        // ✅ PERBAIKAN: Kecualikan transaksi dengan jenis 'pemusnahan'
        $query->where('jenis_transaksi', '!=', 'pemusnahan');

        // Filter pencarian
        $query->when($search, function ($q) use ($search) {
            $q->where(function ($subQuery) use ($search) {
                $subQuery->orWhere('no_surat_persetujuan', 'like', "%{$search}%")
                    ->orWhere('no_ba_serah_terima', 'like', "%{$search}%")
                    ->orWhere('tujuan_sales', 'like', "%{$search}%");

                $subQuery->orWhereHas('item', function ($itemQuery) use ($search) {
                    $itemQuery->where('nama_material', 'like', "%{$search}%")
                        ->orWhere('kode_material', 'like', "%{$search}%");
                });

                $subQuery->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });

                $subQuery->orWhereHas('facilityFrom', function ($facilityQuery) use ($search) {
                    $facilityQuery->where('name', 'like', "%{$search}%");
                });

                $subQuery->orWhereHas('facilityTo', function ($facilityQuery) use ($search) {
                    $facilityQuery->where('name', 'like', "%{$search}%");
                });

                $subQuery->orWhereHas('regionFrom', function ($regionQuery) use ($search) {
                    $regionQuery->where('name_region', 'like', "%{$search}%");
                });

                $subQuery->orWhereHas('regionTo', function ($regionQuery) use ($search) {
                    $regionQuery->where('name_region', 'like', "%{$search}%");
                });
            });
        });

        // Terapkan filter berdasarkan jenis aktivitas/transaksi
        $query->when($jenisTransaksi, function ($q, $jenis) {
            return $q->where('jenis_transaksi', $jenis);
        });

        $query->when($startDate, function ($q, $date) {
            return $q->whereDate('created_at', '>=', $date);
        });

        $query->when($endDate, function ($q, $date) {
            return $q->whereDate('created_at', '<=', $date);
        });

        $transactions = $query->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('dashboard_page.aktivitas_harian.data_transaksi', compact(
            'transactions',
            'search',
            'startDate',
            'endDate',
            'jenisTransaksi'
        ));
    }

    public function exportTransaksiExcel(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
            'jenis_transaksi' => $request->query('jenis_transaksi'),
        ];

        $today = Carbon::now()->isoFormat('dddd, D MMMM YYYY');
        $filename = "Laporan Aktivitas Transaksi - Dicetak {$today}.xlsx";

        return Excel::download(new TransaksiLogExport($filters), $filename);
    }
}