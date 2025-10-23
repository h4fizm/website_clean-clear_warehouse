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

        $currentMonth = now()->month;
        $currentYear = now()->year;

        // ============================
        // Hitungan untuk card statistik (berdasarkan bulan ini)
        // ============================
        $totalSpbe = Facility::where('type', 'SPBE')->count();
        $totalBpt = Facility::where('type', 'BPT')->count();

        // Total material yang di-UPP-kan (status done) pada bulan ini
        $totalUppMaterial = ItemTransaction::query()
            ->where('jenis_transaksi', 'pemusnahan')
            ->where('status', 'done')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('jumlah');

        // Ambil semua transaksi transfer dan sales pada bulan ini
        $allTransactions = ItemTransaction::with(['facilityFrom', 'facilityTo', 'regionFrom', 'regionTo'])
            ->whereIn('jenis_transaksi', ['transfer', 'sales'])
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->get();

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

        // Hitungan khusus untuk Transaksi Sales pada bulan ini
        $totalSalesItems = ItemTransaction::where('jenis_transaksi', 'sales')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('jumlah');

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
            ->selectRaw('nama_material, kode_material, kategori_material, SUM(GREATEST(stok_akhir, 0)) as total_stok_akhir')
            ->groupBy('nama_material', 'kode_material', 'kategori_material')
            ->when($request->filled('search_material'), function ($q) use ($request) {
                $q->having('nama_material', 'like', '%' . $request->search_material . '%')
                    ->orHaving('kode_material', 'like', '%' . $request->search_material . '%');
            })
            ->orderBy('nama_material');

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
        $queryUpp = ItemTransaction::query()
            ->whereNotNull('no_surat_persetujuan')
            ->where('no_surat_persetujuan', '!=', '')
            ->where('jenis_transaksi', 'pemusnahan')
            ->where('no_surat_persetujuan', 'NOT LIKE', '[DELETED_%')
            ->select(
                'no_surat_persetujuan',
                DB::raw('MIN(created_at) as tgl_buat'),
                DB::raw('MAX(updated_at) as tgl_update'),
                DB::raw('MAX(tahapan) as tahapan'),
                DB::raw('MAX(status) as status'),
                DB::raw('SUM(jumlah) as total_material_upp')
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
            'material_base_name' => 'required|string|max:255',
            'capacity' => 'required|integer|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        MaterialCapacity::updateOrCreate(
            [
                'material_base_name' => $request->material_base_name,
                'month' => $request->month,
                'year' => $request->year,
            ],
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

        // Ambil semua item yang cocok dengan nama material
        $allItems = Item::where('nama_material', 'like', $materialBaseName . '%')
            ->with('facility') // Eager load facility untuk akses tipe
            ->get();

        $pusatStock = ['Baru' => 0, 'Baik' => 0, 'Rusak' => 0, 'Afkir' => 0];
        $fasilitasStock = ['Baru' => 0, 'Baik' => 0, 'Rusak' => 0, 'Afkir' => 0];

        // Jika tidak ada item, langsung kembalikan data kosong
        if ($allItems->isEmpty()) {
            $capacity = MaterialCapacity::where('material_base_name', $materialBaseName)
                ->where('month', $month)
                ->where('year', $year)
                ->value('capacity');

            return [
                'stock' => [
                    ['material_name' => $materialBaseName, 'gudang' => 'Gudang Regional', 'baru' => 0, 'baik' => 0, 'rusak' => 0, 'afkir' => 0, 'layak_edar' => 0],
                    ['material_name' => $materialBaseName, 'gudang' => 'SPBE/BPT (Global)', 'baru' => 0, 'baik' => 0, 'rusak' => 0, 'afkir' => 0, 'layak_edar' => 0]
                ],
                'capacity' => $capacity ?? 0,
                'month' => $month,
                'year' => $year,
            ];
        }

        // Gunakan stok_akhir langsung dari tabel `items` yang sudah terupdate
        foreach ($allItems as $item) {
            $kategori = $item->kategori_material;
            $stokAkhir = $item->stok_akhir;

            // ✅ FIX: Pastikan stok tidak negatif - jika negatif, gunakan 0
            $stokAkhir = max(0, $stokAkhir);

            // Cek apakah item berada di gudang pusat
            if ($item->region_id === $pusatRegionId && is_null($item->facility_id)) {
                if (array_key_exists($kategori, $pusatStock)) {
                    $pusatStock[$kategori] += $stokAkhir;
                }
                // Cek apakah item berada di fasilitas (SPBE/BPT)
            } else if (!is_null($item->facility_id)) {
                if (array_key_exists($kategori, $fasilitasStock)) {
                    $fasilitasStock[$kategori] += $stokAkhir;
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

        $capacity = MaterialCapacity::where('material_base_name', $materialBaseName)
            ->where('month', $month)
            ->where('year', $year)
            ->value('capacity');

        return [
            'stock' => $data,
            'capacity' => $capacity ?? 0,
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

    public function getFacilitiesByMaterial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'material_base_name' => 'required|string',
            'kategori_material' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $materialBaseName = $request->input('material_base_name');
        $kategoriMaterial = $request->input('kategori_material');

        $facilities = Item::query()
            ->where('nama_material', 'like', $materialBaseName . '%')
            ->where('kategori_material', $kategoriMaterial)
            ->where('stok_akhir', '>', 0)
            ->with('facility') // Eager load the facility relationship
            ->get();

        $facilitiesData = [];
        foreach ($facilities as $item) {
            // Only consider items that are explicitly linked to a facility (SPBE/BPT)
            // and where the facility type is SPBE or BPT
            if (!is_null($item->facility_id) && $item->facility && in_array($item->facility->type, ['SPBE', 'BPT'])) {
                $facilityName = $item->facility->name;
                if (!isset($facilitiesData[$facilityName])) {
                    $facilitiesData[$facilityName] = 0;
                }
                $facilitiesData[$facilityName] += $item->stok_akhir;
            }
        }

        // Sort facilities alphabetically by name
        ksort($facilitiesData);

        return response()->json([
            'success' => true,
            'facilities' => $facilitiesData,
        ]);

        return response()->json(['success' => true, 'facilities' => $facilities]);
    }
}