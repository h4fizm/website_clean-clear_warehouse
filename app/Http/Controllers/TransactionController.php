<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\Region;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Menampilkan halaman utama transaksi dengan data fasilitas.
     */
    public function index(Request $request)
    {
        // Ambil semua region kecuali 'P.Layang (Pusat)' untuk tombol filter
        $regions = Region::where('name_region', '!=', 'P.Layang (Pusat)')->get();

        // Ambil input dari URL
        $selectedSalesAreaName = $request->query('sales_area', 'SA Jambi');
        $searchQuery = $request->query('search'); // <-- Ambil input pencarian

        // Cari region yang dipilih berdasarkan nama
        $selectedRegion = Region::where('name_region', $selectedSalesAreaName)->first();

        // Ambil fasilitas, tapi jangan langsung eksekusi (tanpa get())
        $facilitiesQuery = $selectedRegion ? $selectedRegion->facilities() : Facility::query()->whereNull('id'); // Query kosong jika region tak ada

        // Tambahkan kondisi pencarian jika ada input search
        if ($searchQuery) {
            $facilitiesQuery->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', '%' . $searchQuery . '%')
                    ->orWhere('kode_plant', 'like', '%' . $searchQuery . '%');
            });
        }

        // Eksekusi query dengan pagination, 5 item per halaman
        $facilities = $facilitiesQuery->paginate(5);

        // Kirim data ke view
        return view('dashboard_page.menu.data_transaksi', [
            'regions' => $regions,
            'facilities' => $facilities,
            'selectedSalesArea' => $selectedSalesAreaName,
            'search' => $searchQuery // <-- Kirim query pencarian kembali ke view
        ]);
    }
}