<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Item;
use App\Models\ItemTransaction;
use App\Models\MaterialCapacity;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Bagian 1 & 2: Kode Anda yang sudah ada tidak diubah
        $user = Auth::user();
        $roleName = $user->getRoleNames()->first() ?? 'User';

        $totalSpbe = Facility::where('type', 'SPBE')->count();
        $totalBpt = Facility::where('type', 'BPT')->count();
        $totalPenerimaan = ItemTransaction::count();
        $totalPenyaluran = $totalPenerimaan;
        $totalUpp = 0;

        $cards = [
            ['title' => 'Total SPBE', 'value' => number_format($totalSpbe), 'icon' => 'fas fa-industry', 'bg' => 'primary', 'link' => '#'],
            ['title' => 'Total BPT', 'value' => number_format($totalBpt), 'icon' => 'fas fa-warehouse', 'bg' => 'info', 'link' => '#'],
            ['title' => 'Transaksi Penerimaan', 'value' => number_format($totalPenerimaan), 'icon' => 'fas fa-arrow-down', 'bg' => 'success', 'link' => '#'],
            ['title' => 'Transaksi Penyaluran', 'value' => number_format($totalPenyaluran), 'icon' => 'fas fa-arrow-up', 'bg' => 'danger', 'link' => '#'],
            ['title' => 'UPP Material', 'value' => $totalUpp, 'icon' => 'fas fa-sync-alt', 'bg' => 'warning', 'link' => '#upp-material-section'],
        ];

        $query = Item::query()
            ->selectRaw('nama_material, kode_material, SUM(stok_awal) as total_stok_awal')
            ->groupBy('nama_material', 'kode_material')
            ->when($request->filled('search_material'), function ($q) use ($request) {
                $q->where('nama_material', 'like', '%' . $request->search_material . '%')
                    ->orWhere('kode_material', 'like', '%' . $request->search_material . '%');
            })
            ->orderBy('nama_material');
        $items = $query->paginate(5)->appends($request->only('search_material'));

        // Bagian 3: Logika untuk Tabel Stok Material Interaktif
        $materialList = $this->getUniqueMaterialBaseNames();
        $defaultMaterialName = $materialList->first() ?? null;
        $initialStockData = $defaultMaterialName ? $this->getFormattedStockData($defaultMaterialName) : [];

        // Bagian 4: Mengirim SEMUA DATA ke view
        return view('dashboard_page.menu.dashboard', [
            'user' => $user,
            'roleName' => $roleName,
            'cards' => $cards,
            'items' => $items,
            'materialList' => $materialList,
            'initialStockData' => $initialStockData,
            'defaultMaterialName' => $defaultMaterialName,
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
                return trim(explode('-', $name)[0]);
            })
            ->unique()->sort()->values();
    }

    /**
     * [FUNGSI DIPERBARUI]
     * Menggunakan Eloquent dan menyederhanakan output.
     */
    private function getFormattedStockData($materialBaseName, $month = null, $year = null)
    {
        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->first();
        $pusatRegionId = $pusatRegion ? $pusatRegion->id : null;

        // Default ke bulan & tahun berjalan
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $items = Item::where('nama_material', 'like', $materialBaseName . '%')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $pusatItems = $items->where('region_id', $pusatRegionId)->whereNull('facility_id');
        $fasilitasItems = $items->whereNotNull('facility_id');

        $calculateStock = function ($collection) {
            $stock = ['baru' => 0, 'baik' => 0, 'rusak' => 0, 'afkir' => 0];
            foreach ($collection as $item) {
                $currentStock = $item->stok_akhir;
                if (str_contains($item->nama_material, 'Baru'))
                    $stock['baru'] += $currentStock;
                if (str_contains($item->nama_material, 'Baik'))
                    $stock['baik'] += $currentStock;
                if (str_contains($item->nama_material, 'Rusak'))
                    $stock['rusak'] += $currentStock;
                if (str_contains($item->nama_material, 'Afkir'))
                    $stock['afkir'] += $currentStock;
            }
            return $stock;
        };

        $pusatStock = $calculateStock($pusatItems);
        $fasilitasStock = $calculateStock($fasilitasItems);

        $data = [
            [
                'material_name' => $materialBaseName,
                'gudang' => 'Gudang Region',
                'baru' => $pusatStock['baru'],
                'baik' => $pusatStock['baik'],
                'rusak' => $pusatStock['rusak'],
                'afkir' => $pusatStock['afkir'],
                'layak_edar' => $pusatStock['baru'] + $pusatStock['baik'],
            ],
            [
                'material_name' => $materialBaseName,
                'gudang' => 'SPBE/BPT (Global)',
                'baru' => $fasilitasStock['baru'],
                'baik' => $fasilitasStock['baik'],
                'rusak' => $fasilitasStock['rusak'],
                'afkir' => $fasilitasStock['afkir'],
                'layak_edar' => $fasilitasStock['baru'] + $fasilitasStock['baik'],
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

    public function getStockDataApi($materialBaseName, Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year');

        $data = $this->getFormattedStockData($materialBaseName, $month, $year);
        return response()->json($data);
    }

}
