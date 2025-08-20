<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PusatController extends Controller
{
    /**
     * MODIFIKASI: Logika filter tanggal diperbaiki dan disederhanakan.
     */
    public function index(Request $request)
    {
        // Ambil semua input filter dari request
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        // [PERBAIKAN] Mulai query menggunakan Model 'Item'
        $query = Item::query();

        // [PENTING] Filter ini untuk memastikan hanya data P.Layang (Pusat) yang diambil.
        // Berdasarkan model Anda, item P.Layang memiliki region_id dan tidak memiliki facility_id.
        $query->whereNotNull('region_id')->whereNull('facility_id');

        // Terapkan filter PENCARIAN jika ada
        $query->when($filters['search'], function ($q, $search) {
            return $q->where('nama_material', 'like', '%' . $search . '%');
        });

        // Terapkan filter TANGGAL AWAL jika ada
        $query->when($filters['start_date'], function ($q, $startDate) {
            return $q->whereDate('updated_at', '>=', $startDate);
        });

        // Terapkan filter TANGGAL AKHIR jika ada
        $query->when($filters['end_date'], function ($q, $endDate) {
            return $q->whereDate('updated_at', '<=', $endDate);
        });

        // Ambil data setelah difilter dengan pagination dan urutkan
        $items = $query->latest('updated_at')->paginate(10);

        // Kirim data dan nilai filter ke view
        return view('dashboard_page.menu.data_pusat', compact('items', 'filters'));
    }

    // Method lainnya tidak perlu diubah
    public function create()
    {
        return view('dashboard_page.menu.tambah_material');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')],
            'kode_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')],
            'total_stok' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        Item::create([
            'nama_material' => $request->nama_material,
            'kode_material' => $request->kode_material,
            'stok_awal' => $request->total_stok,
            'facility_id' => null,
            'region_id' => 1,
        ]);
        return response()->json(['success' => true, 'message' => 'Data material berhasil ditambahkan!', 'redirect_url' => route('pusat.index')], 201);
    }

    public function update(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'nama_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')->ignore($item->id)],
            'kode_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')->ignore($item->id)],
            'stok_awal' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_item_id', $item->id);
        }

        $item->update($request->only(['nama_material', 'kode_material', 'stok_awal']));

        return redirect()->route('pusat.index')->with('success', 'Data material berhasil diperbarui!');
    }

    public function destroy(Item $item)
    {
        if ($item->transactions()->exists()) {
            return redirect()->route('pusat.index')->with('error', 'Gagal menghapus! Material ini memiliki riwayat transaksi.');
        }

        $item->delete();
        return redirect()->route('pusat.index')->with('success', 'Data material berhasil dihapus!');
    }
}