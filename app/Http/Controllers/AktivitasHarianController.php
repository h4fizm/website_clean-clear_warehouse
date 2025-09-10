<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemTransaction;
use App\Exports\TransaksiLogExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class AktivitasHarianController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $jenisTransaksi = $request->input('jenis_transaksi');

        // Membangun kueri dasar dengan eager loading untuk performa
        $query = ItemTransaction::with([
            'item' => fn($q) => $q->withTrashed(),
            'user',
            'facilityFrom',
            'facilityTo',
            'regionFrom',
            'regionTo',
        ]);

        // Memastikan hanya transaksi non-pemusnahan yang ditampilkan secara default
        $query->where(function ($q) use ($jenisTransaksi) {
            if (!$jenisTransaksi) {
                $q->where('jenis_transaksi', '!=', 'pemusnahan');
            } else {
                $q->where('jenis_transaksi', $jenisTransaksi);
            }
        });

        // Filter pencarian berdasarkan berbagai kolom
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

        // Filter tanggal
        $query->when($startDate, function ($q, $date) {
            return $q->whereDate('created_at', '>=', $date);
        });

        $query->when($endDate, function ($q, $date) {
            return $q->whereDate('created_at', '<=', $date);
        });

        $transactions = $query->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        // Mengembalikan tampilan dengan data yang telah difilter dan parameter filter
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

        // Tambahkan filter default agar pemusnahan tidak ter-export secara default
        if (empty($filters['jenis_transaksi'])) {
            $filters['jenis_transaksi'] = ['!=', 'pemusnahan'];
        }

        $today = Carbon::now()->isoFormat('dddd, D MMMM YYYY');
        $filename = "Laporan Aktivitas Transaksi - Dicetak {$today}.xlsx";

        return Excel::download(new TransaksiLogExport($filters), $filename);
    }
}