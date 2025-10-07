<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ItemTransaction;
use App\Exports\TransaksiLogExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;

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
            'item',
            'user',
            'facilityFrom',
            'facilityTo',
            'regionFrom',
            'regionTo',
        ]);

        // Memastikan hanya transaksi non-pemusnahan yang ditampilkan secara default
        $query->where(function ($q) use ($jenisTransaksi) {
            if (!$jenisTransaksi) {
                // ✅ PERBAIKAN: Menampilkan semua transaksi kecuali 'pemusnahan'
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

                // ✅ PERBAIKAN: Menggunakan `whereHas` untuk relasi `facilityFrom`
                $subQuery->orWhereHas('facilityFrom', function ($facilityQuery) use ($search) {
                    $facilityQuery->where('name', 'like', "%{$search}%");
                });

                // ✅ PERBAIKAN: Menggunakan `whereHas` untuk relasi `facilityTo`
                $subQuery->orWhereHas('facilityTo', function ($facilityQuery) use ($search) {
                    $facilityQuery->where('name', 'like', "%{$search}%");
                });

                // ✅ PERBAIKAN: Menggunakan `whereHas` untuk relasi `regionFrom`
                $subQuery->orWhereHas('regionFrom', function ($regionQuery) use ($search) {
                    $regionQuery->where('name_region', 'like', "%{$search}%");
                });

                // ✅ PERBAIKAN: Menggunakan `whereHas` untuk relasi `regionTo`
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

        return view('dashboard_page.aktivitas_harian.data_transaksi', compact(
            'transactions',
            'search',
            'startDate',
            'endDate',
            'jenisTransaksi'
        ));
    }

    /**
     * Metode untuk mencatat transaksi secara terpusat.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logTransaksi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'user_id' => 'required|exists:users,id',
            // ✅ PERBAIKAN: Tambahkan 'transfer' ke dalam aturan validasi
            'jenis_transaksi' => 'required|in:penyaluran,penerimaan,sales,pemusnahan,transfer',
            'jumlah' => 'required|integer|min:1',
            'stok_awal_asal' => 'required|integer',
            'stok_akhir_asal' => 'required|integer',
            'created_at' => 'required|date',
            'updated_at' => 'required|date',
            'facility_from' => 'nullable|exists:facilities,id',
            'facility_to' => 'nullable|exists:facilities,id',
            'region_from' => 'nullable|exists:regions,id',
            'region_to' => 'nullable|exists:regions,id',
            'no_surat_persetujuan' => 'nullable|string',
            'no_ba_serah_terima' => 'nullable|string',
            'tujuan_sales' => 'nullable|string|in:Vendor UPP,Sales Agen,Sales BPT,Sales SPBE',
            'keterangan_transaksi' => 'nullable|string',
            'tahapan' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            ItemTransaction::create($request->all());
            return response()->json(['success' => true, 'message' => 'Transaksi berhasil dicatat.'], 201);
        } catch (\Exception $e) {
            \Log::error('Log Transaksi Error: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mencatat transaksi.'], 500);
        }
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

    /**
     * API endpoint for DataTables to fetch aktivitas transaksi data
     */
    public function getAktivitasTransaksi(Request $request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'] ?? '';
        $order = $request->get('order')[0] ?? null;
        $columns = $request->get('columns') ?? [];
        $jenisTransaksi = $request->get('jenis_transaksi');

        // Build the query with optimized joins
        $query = ItemTransaction::with([
            'item',
            'user',
            'facilityFrom',
            'facilityTo',
            'regionFrom',
            'regionTo',
        ]);

        // Filter for non-pemusnahan transactions by default
        $query->where(function ($q) use ($jenisTransaksi) {
            if (!$jenisTransaksi) {
                $q->where('jenis_transaksi', '!=', 'pemusnahan');
            } else {
                $q->where('jenis_transaksi', $jenisTransaksi);
            }
        });

        // Add search functionality with optimized queries
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('no_surat_persetujuan', 'like', "%{$search}%")
                  ->orWhere('no_ba_serah_terima', 'like', "%{$search}%")
                  ->orWhere('tujuan_sales', 'like', "%{$search}%");

                $q->orWhereHas('item', function ($itemQuery) use ($search) {
                    $itemQuery->where('nama_material', 'like', "%{$search}%")
                              ->orWhere('kode_material', 'like', "%{$search}%");
                });

                $q->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                });

                $q->orWhereHas('facilityFrom', function ($facilityQuery) use ($search) {
                    $facilityQuery->where('name', 'like', "%{$search}%");
                });

                $q->orWhereHas('facilityTo', function ($facilityQuery) use ($search) {
                    $facilityQuery->where('name', 'like', "%{$search}%");
                });

                $q->orWhereHas('regionFrom', function ($regionQuery) use ($search) {
                    $regionQuery->where('name_region', 'like', "%{$search}%");
                });

                $q->orWhereHas('regionTo', function ($regionQuery) use ($search) {
                    $regionQuery->where('name_region', 'like', "%{$search}%");
                });
            });
        }

        // Get total records count
        $totalRecords = $query->count();

        // Add ordering
        if ($order) {
            $columnIndex = $order['column'];
            $direction = $order['dir'];
            $columnName = $columns[$columnIndex]['data'] ?? 'id';

            switch ($columnName) {
                case 'no_surat_persetujuan':
                    $query->orderBy('no_surat_persetujuan', $direction);
                    break;
                case 'item.nama_material':
                    $query->orderBy('item.nama_material', $direction);
                    break;
                case 'jenis_transaksi':
                    $query->orderBy('jenis_transaksi', $direction);
                    break;
                case 'jumlah':
                    $query->orderBy('jumlah', $direction);
                    break;
                case 'created_at':
                    $query->orderBy('created_at', $direction);
                    break;
                default:
                    $query->orderBy('created_at', $direction);
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Get the filtered records count before pagination
        $filteredQuery = clone $query;
        $filteredRecords = $filteredQuery->count();

        // Add pagination - ensure both parameters are provided
        if ($length > 0) {
            $query->skip($start)->take($length);
        }

        $transactions = $query->get();

        $data = [];
        foreach ($transactions as $transaction) {
            $data[] = [
                'id' => $transaction->id,
                'no_surat_persetujuan' => $transaction->no_surat_persetujuan ?? '-',
                'item' => $transaction->item ? $transaction->item->nama_material : '-',
                'item.nama_material' => $transaction->item ? $transaction->item->nama_material : '-',
                'jenis_transaksi' => ucfirst($transaction->jenis_transaksi),
                'jumlah' => number_format($transaction->jumlah, 0, ',', '.'),
                'facility_from' => $transaction->facilityFrom ? $transaction->facilityFrom->name : ($transaction->regionFrom ? $transaction->regionFrom->name_region : '-'),
                'facility_to' => $transaction->facilityTo ? $transaction->facilityTo->name : ($transaction->regionTo ? $transaction->regionTo->name_region : '-'),
                'user' => $transaction->user ? $transaction->user->name : '-',
                'user.name' => $transaction->user ? $transaction->user->name : '-',
                'created_at' => Carbon::parse($transaction->created_at)->format('d-m-Y H:i:s'),
            ];
        }

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
}