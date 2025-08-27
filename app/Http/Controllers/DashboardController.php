<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Item;
use App\Models\ItemTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama dengan data yang dinamis.
     */
    public function index(Request $request)
    {
        // 1. Mengambil data user yang sedang login untuk Welcome Card
        $user = Auth::user();
        $roleName = $user->getRoleNames()->first() ?? 'User';

        // 2. Menghitung data untuk Statistik Cards
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

        // 3. Mengambil data material dari PUSAT SAJA dengan paginasi dan search
        // ✅ PERUBAHAN DI SINI: Ditambahkan ->whereNull('facility_id')
        $query = Item::whereNull('facility_id');

        if ($request->has('search_material') && $request->search_material != '') {
            $searchTerm = $request->search_material;
            // Query di dalam where untuk memastikan kondisi whereNull tetap berlaku
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_material', 'like', '%' . $searchTerm . '%')
                    ->orWhere('kode_material', 'like', '%' . $searchTerm . '%');
            });
        }

        $items = $query->latest()->paginate(5)->appends($request->only('search_material'));

        // Kirim semua data ke view
        return view('dashboard_page.menu.dashboard', [
            'user' => $user,
            'roleName' => $roleName,
            'cards' => $cards,
            'items' => $items
        ]);
    }
}