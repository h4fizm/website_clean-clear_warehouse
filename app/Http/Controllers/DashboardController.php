<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Item;
use App\Models\ItemTransaction;
use App\Models\MaterialCapacity;
use App\Models\Region;
use App\Exports\AllMaterialStockExport;
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

        // ============================
        // UPP Material
        // ============================

        // ✅ Versi aktif (jumlah dokumen UPP)
        $totalUpp = ItemTransaction::query()
            ->whereNotNull('no_surat_persetujuan')
            ->where('no_surat_persetujuan', '!=', '')
            ->where('status', 'done')
            ->distinct('no_surat_persetujuan')
            ->count('no_surat_persetujuan');

        /*
        // 🔄 Versi alternatif (stok UPP / jumlah tabung dalam UPP)
        $totalUpp = ItemTransaction::query()
            ->whereNotNull('no_surat_persetujuan')
            ->where('no_surat_persetujuan', '!=', '')
            ->where('status', 'done')
            ->sum('jumlah');
        */

        // ============================
        // Hitungan Transaksi (Aktif) → pakai jumlah baris transaksi
        // ============================
        $allTransactions = ItemTransaction::with(['facilityFrom', 'facilityTo', 'regionFrom', 'regionTo'])
            ->where('jenis_transaksi', '!=', 'pemusnahan')
            ->get();

        $totalPenyaluran = 0;
        $totalPenerimaan = 0;

        foreach ($allTransactions as $trx) {
            if ($trx->jenis_transaksi === 'sales') {
                // Sales dihitung sebagai 1 penyaluran
                $totalPenyaluran++;
            } else {
                if ($trx->facility_from || $trx->region_from) {
                    $totalPenyaluran++;
                }
                if ($trx->facility_to || $trx->region_to) {
                    $totalPenerimaan++;
                }
            }
        }

        /*
        // ============================
        // Hitungan Transaksi (Alternatif) → pakai stok/jumlah tabung
        // ============================
        $totalPenyaluran = ItemTransaction::query()
            ->where(function ($q) {
                $q->whereNotNull('facility_from')
                    ->orWhereNotNull('region_from')
                    ->orWhere('jenis_transaksi', 'sales');
            })
            ->sum('jumlah');

        $totalPenerimaan = ItemTransaction::query()
            ->where(function ($q) {
                $q->whereNotNull('facility_to')
                    ->orWhereNotNull('region_to');
            })
            ->whereNull('tujuan_sales')
            ->sum('jumlah');
        */

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
            ->selectRaw('nama_material, kode_material, SUM(stok_akhir) as total_stok_akhir') // ✅ PERBAIKAN: Menggunakan stok_akhir
            ->groupBy('nama_material', 'kode_material')
            ->when($request->filled('search_material'), function ($q) use ($request) {
                $q->where('nama_material', 'like', '%' . $request->search_material . '%')
                    ->orWhere('kode_material', 'like', '%' . $request->search_material . '%');
            })
            ->orderBy('nama_material');

        $items = $query->paginate(5)->appends($request->only('search_material'));

        $materialList = $this->getUniqueMaterialBaseNames();
        $defaultMaterialName = $materialList->first() ?? null;
        $initialStockData = $defaultMaterialName ? $this->getFormattedStockData($defaultMaterialName) : [];

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
                $parts = explode(' - ', $name);
                return $parts[0] ?? $name;
            })
            ->unique()->sort()->values();
    }

    private function getFormattedStockData($materialBaseName, $month = null, $year = null)
    {
        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->first();
        $pusatRegionId = $pusatRegion ? $pusatRegion->id : null;

        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        // Ambil data dari tabel `items` dengan filtering nama material dan tanggal
        // Kita asumsikan `stok_akhir` di tabel `items` sudah ter-update
        // Berdasarkan logika yang sudah ada di controller sebelumnya.
        $items = Item::where('nama_material', 'like', $materialBaseName . '%')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $pusatItems = $items->where('region_id', $pusatRegionId)->whereNull('facility_id');
        $fasilitasItems = $items->whereNotNull('facility_id');

        // Menggunakan array untuk menampung total stok per kategori
        $pusatStock = ['Baru' => 0, 'Baik' => 0, 'Rusak' => 0, 'Afkir' => 0];
        $fasilitasStock = ['Baru' => 0, 'Baik' => 0, 'Rusak' => 0, 'Afkir' => 0];

        // Hitung stok untuk gudang pusat
        foreach ($pusatItems as $item) {
            $kategori = $item->kategori_material;
            if (array_key_exists($kategori, $pusatStock)) {
                $pusatStock[$kategori] += $item->stok_akhir;
            }
        }

        // Hitung stok untuk gudang fasilitas (SPBE/BPT)
        foreach ($fasilitasItems as $item) {
            $kategori = $item->kategori_material;
            if (array_key_exists($kategori, $fasilitasStock)) {
                $fasilitasStock[$kategori] += $item->stok_akhir;
            }
        }

        $data = [
            [
                'material_name' => $materialBaseName,
                'gudang' => 'Gudang Region',
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

    public function getStockDataApi($materialBaseName, Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year');

        $data = $this->getFormattedStockData($materialBaseName, $month, $year);
        return response()->json($data);
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
