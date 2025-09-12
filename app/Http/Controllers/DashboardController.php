<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Item;
use App\Models\ItemTransaction;
use App\Models\MaterialCapacity;
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

        // ============================
        // Hitungan untuk card statistik
        // ============================
        $totalSpbe = Facility::where('type', 'SPBE')->count();
        $totalBpt = Facility::where('type', 'BPT')->count();

        $totalUpp = ItemTransaction::query()
            ->whereNotNull('no_surat_persetujuan')
            ->where('no_surat_persetujuan', '!=', '')
            ->where('jenis_transaksi', 'pemusnahan')
            ->where('status', 'done')
            ->distinct('no_surat_persetujuan')
            ->count('no_surat_persetujuan');

        $allTransactions = ItemTransaction::with(['facilityFrom', 'facilityTo', 'regionFrom', 'regionTo'])
            ->whereIn('jenis_transaksi', ['transfer', 'sales'])
            ->get();

        // Perbaikan logika hitungan transaksi
        $totalPenyaluran = 0;
        $totalPenerimaan = 0;
        foreach ($allTransactions as $trx) {
            if ($trx->jenis_transaksi === 'sales') {
                $totalPenyaluran += $trx->jumlah;
            } else {
                if ($trx->region_from === 1 && $trx->region_to !== 1) { // Asumsi region pusat ID 1
                    $totalPenyaluran += $trx->jumlah;
                }
                if ($trx->region_from !== 1 && $trx->region_to === 1) { // Asumsi region pusat ID 1
                    $totalPenerimaan += $trx->jumlah;
                }
            }
        }

        // ============================
        // Card Dashboard
        // ============================
        $cards = [
            ['title' => 'Total SPBE', 'value' => number_format($totalSpbe), 'icon' => 'fas fa-industry', 'bg' => 'primary', 'link' => '#'],
            ['title' => 'Total BPT', 'value' => number_format($totalBpt), 'icon' => 'fas fa-warehouse', 'bg' => 'info', 'link' => '#'],
            ['title' => 'Transaksi Penerimaan', 'value' => number_format($totalPenerimaan), 'icon' => 'fas fa-arrow-down', 'bg' => 'success', 'link' => '#'],
            ['title' => 'Transaksi Penyaluran', 'value' => number_format($totalPenyaluran), 'icon' => 'fas fa-arrow-up', 'bg' => 'danger', 'link' => '#'],
            ['title' => 'UPP Material', 'value' => number_format($totalUpp), 'icon' => 'fas fa-sync-alt', 'bg' => 'warning', 'link' => '#'],
        ];

        // ============================
        // Data tabel material
        // ============================
        $query = Item::query()
            ->selectRaw('nama_material, kode_material, kategori_material, SUM(stok_akhir) as total_stok_akhir')
            ->groupBy('nama_material', 'kode_material', 'kategori_material')
            ->when($request->filled('search_material'), function ($q) use ($request) {
                $q->where('nama_material', 'like', '%' . $request->search_material . '%')
                    ->orWhere('kode_material', 'like', '%' . $request->search_material . '%');
            })
            ->orderBy('nama_material');

        $items = $query->paginate(5)->appends($request->only('search_material'));

        $materialList = $this->getUniqueMaterialBaseNames();
        $defaultMaterialName = $materialList->first() ?? null;
        $initialStockData = $defaultMaterialName ? $this->getFormattedStockData($defaultMaterialName) : [];

        // ✅ PERBAIKAN: Menggunakan created_at dan updated_at
        $queryUpp = ItemTransaction::query()
            ->whereNotNull('no_surat_persetujuan')
            ->where('no_surat_persetujuan', '!=', '')
            ->where('jenis_transaksi', 'pemusnahan')
            ->select(
                'no_surat_persetujuan',
                DB::raw('MIN(created_at) as tgl_buat'),
                DB::raw('MAX(updated_at) as tgl_update'),
                DB::raw('MAX(tahapan) as tahapan'),
                DB::raw('MAX(status) as status')
            )
            ->groupBy('no_surat_persetujuan')
            ->when($request->filled('search_upp'), function ($q) use ($request) {
                $q->having('no_surat_persetujuan', 'like', '%' . $request->search_upp . '%');
            })
            ->when($request->filled(['start_date_upp', 'end_date_upp']), function ($q) use ($request) {
                $startDate = Carbon::parse($request->start_date_upp)->startOfDay();
                $endDate = Carbon::parse($request->end_date_upp)->endOfDay();
                $q->havingRaw('MIN(created_at) >= ? AND MAX(updated_at) <= ?', [$startDate, $endDate]);
            })
            ->orderByRaw('MIN(created_at) DESC');

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
            'material_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        MaterialCapacity::updateOrCreate(
            ['material_base_name' => $request->material_name],
            ['capacity' => $request->capacity]
        );

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
        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->first();
        $pusatRegionId = $pusatRegion ? $pusatRegion->id : null;

        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        // Mendapatkan semua item yang relevan
        $allItemsQuery = Item::where('nama_material', 'like', $materialBaseName . '%')
            ->with([
                'transactions' => function ($query) use ($month, $year) {
                    $query->whereMonth('created_at', $month)->whereYear('created_at', $year);
                }
            ]);

        $items = $allItemsQuery->get();

        // Menggunakan array untuk menampung total stok per kategori
        $pusatStock = ['Baru' => 0, 'Baik' => 0, 'Rusak' => 0, 'Afkir' => 0];
        $fasilitasStock = ['Baru' => 0, 'Baik' => 0, 'Rusak' => 0, 'Afkir' => 0];

        // Hitung stok dari data item, bukan dari transaksi
        foreach ($items as $item) {
            $kategori = $item->kategori_material;
            if ($item->region_id === $pusatRegionId && is_null($item->facility_id)) {
                if (array_key_exists($kategori, $pusatStock)) {
                    $pusatStock[$kategori] += $item->stok_akhir;
                }
            } else if (!is_null($item->facility_id)) {
                if (array_key_exists($kategori, $fasilitasStock)) {
                    $fasilitasStock[$kategori] += $item->stok_akhir;
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

        $capacity = MaterialCapacity::where('material_base_name', $materialBaseName)->value('capacity');

        return [
            'stock' => $data,
            'capacity' => $capacity ?? 0,
            'month' => $month,
            'year' => $year,
        ];
    }

    public function exportExcel(Request $request)
    {
        $filters = [
            'start_date' => $request->input('start_date'),
            'end_date' => $request->input('end_date'),
        ];

        return Excel::download(new AllMaterialStockExport($filters), 'Total Stok Material.xlsx');
    }
}