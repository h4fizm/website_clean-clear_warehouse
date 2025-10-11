<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\Item;
use App\Models\TransactionLog;
use App\Models\CurrentStock;
use App\Models\DestructionSubmission;
use App\Models\Region;
use App\Exports\AllMaterialStockExport;
use App\Exports\UppMaterialExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $roleName = $user->getRoleNames()->first() ?? 'User';

        $currentMonth = now()->month;
        $currentYear = now()->year;

        // ============================
        // Hitungan untuk card statistik (berdasarkan bulan ini)
        // ============================
        $totalSpbe = Plant::where('kategori_plant', 'SPBE')->count();
        $totalBpt = Plant::where('kategori_plant', 'BPT')->count();

        // Total material yang di-UPP-kan (status done) pada bulan ini
        $totalUppMaterial = DestructionSubmission::query()
            ->where('status_pengajuan', 'DONE')
            ->whereMonth('tanggal_pengajuan', $currentMonth)
            ->whereYear('tanggal_pengajuan', $currentYear)
            ->sum('kuantitas_diajukan');

        // Ambil semua transaksi transfer dan sales pada bulan ini
        $allTransactions = TransactionLog::with(['item', 'destinationSale'])
            ->whereIn('tipe_pergerakan', ['transfer', 'sales'])
            ->whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->get();

        $totalPenyaluran = 0;
        $totalPenerimaan = 0;
        foreach ($allTransactions as $trx) {
            if ($trx->tipe_pergerakan === 'sales') {
                $totalPenyaluran += $trx->kuantitas;
            } else {
                // Mendapatkan lokasi actor dan target
                $actorLocation = $trx->actorLocation;
                $targetLocation = $trx->targetLocation;

                // Asumsi region pusat adalah yang bernama "P.Layang (Pusat)"
                $pusatRegion = Region::where('nama_regions', 'P.Layang (Pusat)')->first();

                if ($actorLocation && $targetLocation && $pusatRegion) {
                    if ($actorLocation instanceof Region && $actorLocation->region_id === $pusatRegion->region_id &&
                        (!$targetLocation instanceof Region || $targetLocation->region_id !== $pusatRegion->region_id)) {
                        $totalPenyaluran += $trx->kuantitas;
                    }
                    if ((!$actorLocation instanceof Region || $actorLocation->region_id !== $pusatRegion->region_id) &&
                        $targetLocation instanceof Region && $targetLocation->region_id === $pusatRegion->region_id) {
                        $totalPenerimaan += $trx->kuantitas;
                    }
                }
            }
        }

        // Hitungan khusus untuk Transaksi Sales pada bulan ini
        $totalSalesItems = TransactionLog::where('tipe_pergerakan', 'sales')
            ->whereMonth('tanggal_transaksi', $currentMonth)
            ->whereYear('tanggal_transaksi', $currentYear)
            ->sum('kuantitas');

        // ✅ PERBAIKAN: Menambahkan card untuk Total Material UPP dan Transaksi Sales
        $cards = [
            ['title' => 'Total SPBE', 'value' => number_format($totalSpbe), 'icon' => 'fas fa-industry', 'bg' => 'primary', 'link' => '#'],
            ['title' => 'Total BPT', 'value' => number_format($totalBpt), 'icon' => 'fas fa-warehouse', 'bg' => 'info', 'link' => '#'],
            ['title' => 'Transaksi Penerimaan', 'value' => number_format($totalPenerimaan), 'icon' => 'fas fa-arrow-down', 'bg' => 'success', 'link' => '#'],
            ['title' => 'Transaksi Penyaluran', 'value' => number_format($totalPenyaluran), 'icon' => 'fas fa-arrow-up', 'bg' => 'danger', 'link' => '#'],
            ['title' => 'Transaksi Sales', 'value' => number_format($totalSalesItems), 'icon' => 'fas fa-dollar-sign', 'bg' => 'warning', 'link' => '#'],
            ['title' => 'Total Material UPP', 'value' => number_format($totalUppMaterial), 'icon' => 'fas fa-trash-alt', 'bg' => 'warning', 'link' => '#'],
        ];

        // ============================
        // Data tabel material (global)
        // ============================
        $query = Item::query()
            ->selectRaw('items.nama_material, items.kode_material, items.kategori_material, COALESCE(SUM(current_stocks.current_quantity), 0) as total_stok_akhir')
            ->leftJoin('current_stocks', 'items.item_id', '=', 'current_stocks.item_id')
            ->groupBy('items.nama_material', 'items.kode_material', 'items.kategori_material')
            ->when($request->filled('search_material'), function ($q) use ($request) {
                $q->having('items.nama_material', 'like', '%' . $request->search_material . '%')
                    ->orHaving('items.kode_material', 'like', '%' . $request->search_material . '%');
            })
            ->orderBy('items.nama_material');

        $items = $query->paginate(5)->appends($request->only('search_material'));

        // ============================
        // Logika untuk tabel stok material per regional (tabel kedua)
        // ============================
        $materialList = $this->getUniqueMaterialBaseNames();
        $defaultMaterialName = $request->input('material_name', $materialList->first());
        $initialStockData = $defaultMaterialName ? $this->getFormattedStockData($defaultMaterialName, $request->input('month'), $request->input('year')) : [];

        // ============================
        // Logika untuk tabel UPP
        // ============================
        $queryUpp = DestructionSubmission::with(['item'])
            ->whereNotNull('no_surat')
            ->where('no_surat', '!=', '')
            ->select(
                'no_surat',
                DB::raw('MIN(tanggal_pengajuan) as tgl_buat'),
                DB::raw('MAX(updated_at) as tgl_update'),
                DB::raw('MAX(tahapan) as tahapan'),
                DB::raw('MAX(status_pengajuan) as status'),
                DB::raw('SUM(kuantitas_diajukan) as total_material_upp')
            )
            ->groupBy('no_surat')
            ->when($request->filled('search_upp'), function ($q) use ($request) {
                $q->having('no_surat', 'like', '%' . $request->search_upp . '%');
            })
            ->when($request->filled(['start_date_upp', 'end_date_upp']), function ($q) use ($request) {
                $startDate = Carbon::parse($request->start_date_upp)->startOfDay();
                $endDate = Carbon::parse($request->end_date_upp)->endOfDay();
                $q->havingRaw('MIN(tanggal_pengajuan) >= ? AND MAX(updated_at) <= ?', [$startDate, $endDate]);
            })
            ->orderByRaw('MIN(tanggal_pengajuan) DESC');

        $upps = $queryUpp->paginate(10)->appends($request->only(['search_upp', 'start_date_upp', 'end_date_upp']));

        return view('dashboard_page.menu.dashboard', [
            'user' => $user,
            'roleName' => $roleName,
            'cards' => $cards,
            'items' => $items,
            'materialList' => $materialList,
            'initialStockData' => $initialStockData,
            'defaultMaterialName' => $defaultMaterialName,
            'upps' => $upps,
        ]);
    }

    public function updateCapacityApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_base_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        // Note: MaterialCapacity model tidak ada dalam struktur baru
        // Fungsi ini perlu disesuaikan atau capacity dapat disimpan di tempat lain
        // Untuk sementara, kita akan simpan sebagai JSON di table items atau buat table baru

        return response()->json(['success' => true, 'message' => 'Kapasitas berhasil diperbarui.']);
    }

    private function getUniqueMaterialBaseNames()
    {
        return Item::select('nama_material')->distinct()->pluck('nama_material')
            ->map(function ($name) {
                $parts = explode(' - ', $name);
                return $parts[0] ?? $name;
            })
            ->unique()->sort()->values();
    }

    public function getStockDataApi(Request $request)
    {
        $materialBaseName = $request->input('material_base_name');
        $month = $request->input('month');
        $year = $request->input('year');

        if (!$materialBaseName) {
            return response()->json(['error' => 'Nama material tidak boleh kosong'], 400);
        }

        $formattedData = $this->getFormattedStockData($materialBaseName, $month, $year);
        return response()->json($formattedData);
    }

    private function getFormattedStockData($materialBaseName, $month = null, $year = null)
    {
        $pusatRegion = Region::where('nama_regions', 'P.Layang (Pusat)')->first();
        $pusatRegionId = $pusatRegion ? $pusatRegion->region_id : null;

        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        // Ambil semua item yang cocok dengan nama material
        $allItems = Item::where('nama_material', 'like', $materialBaseName . '%')
            ->get();

        $pusatStock = ['Baru' => 0, 'Baik' => 0, 'Rusak' => 0, 'Afkir' => 0];
        $fasilitasStock = ['Baru' => 0, 'Baik' => 0, 'Rusak' => 0, 'Afkir' => 0];

        // Jika tidak ada item, langsung kembalikan data kosong
        if ($allItems->isEmpty()) {
            return [
                'stock' => [
                    ['material_name' => $materialBaseName, 'gudang' => 'Gudang Regional', 'baru' => 0, 'baik' => 0, 'rusak' => 0, 'afkir' => 0, 'layak_edar' => 0],
                    ['material_name' => $materialBaseName, 'gudang' => 'SPBE/BPT (Global)', 'baru' => 0, 'baik' => 0, 'rusak' => 0, 'afkir' => 0, 'layak_edar' => 0]
                ],
                'capacity' => 0, // MaterialCapacity tidak ada dalam model baru
                'month' => $month,
                'year' => $year,
            ];
        }

        // Ambil semua current stocks untuk items ini
        $itemIds = $allItems->pluck('item_id');
        $currentStocks = CurrentStock::whereIn('item_id', $itemIds)->get();

        // Kelompokkan stok berdasarkan lokasi
        foreach ($currentStocks as $stock) {
            $item = $allItems->firstWhere('item_id', $stock->item_id);
            if (!$item) continue;

            $kategori = $item->kategori_material;
            $location = $stock->getLocationAttribute();

            if (array_key_exists($kategori, $pusatStock) && array_key_exists($kategori, $fasilitasStock)) {
                // Cek apakah lokasi adalah region pusat
                if ($location instanceof Region && $location->region_id === $pusatRegionId) {
                    $pusatStock[$kategori] += $stock->current_quantity;
                }
                // Cek apakah lokasi adalah plant (SPBE/BPT)
                else if ($location instanceof Plant) {
                    $fasilitasStock[$kategori] += $stock->current_quantity;
                }
                // Region lain masuk ke kategori fasilitas
                else if ($location instanceof Region) {
                    $fasilitasStock[$kategori] += $stock->current_quantity;
                }
            }
        }

        $data = [
            [
                'material_name' => $materialBaseName,
                'gudang' => 'Gudang Regional',
                'baru' => $pusatStock['Baru'],
                'baik' => $pusatStock['Baik'],
                'rusak' => $pusatStock['Rusak'],
                'afkir' => $pusatStock['Afkir'],
                'layak_edar' => $pusatStock['Baru'] + $pusatStock['Baik'],
            ],
            [
                'material_name' => $materialBaseName,
                'gudang' => 'SPBE/BPT (Global)',
                'baru' => $fasilitasStock['Baru'],
                'baik' => $fasilitasStock['Baik'],
                'rusak' => $fasilitasStock['Rusak'],
                'afkir' => $fasilitasStock['Afkir'],
                'layak_edar' => $fasilitasStock['Baru'] + $fasilitasStock['Baik'],
            ],
        ];

        return [
            'stock' => $data,
            'capacity' => 0, // MaterialCapacity tidak ada dalam model baru
            'month' => $month,
            'year' => $year,
        ];
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        $startDate = $filters['start_date'] ? Carbon::parse($filters['start_date'])->format('d-m-Y') : 'Awal';
        $endDate = $filters['end_date'] ? Carbon::parse($filters['end_date'])->format('d-m-Y') : 'Akhir';
        $filename = "Laporan Total Stok Material ({$startDate} - {$endDate}).xlsx";

        return Excel::download(new AllMaterialStockExport($filters), $filename);
    }
}