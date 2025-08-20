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
     * Menampilkan halaman data P.Layang (Pusat).
     */
    public function index(Request $request)
    {
        // ... (Kode tidak berubah)
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $query = Item::whereNull('facility_id')->with('transactions');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_material', 'like', "%{$search}%")
                    ->orWhere('kode_material', 'like', "%{$search}%");
            });
        }
        if ($startDate) {
            $query->whereHas('transactions', function ($q) use ($startDate) {
                $q->whereDate('created_at', '>=', $startDate);
            });
        }
        if ($endDate) {
            $query->whereHas('transactions', function ($q) use ($endDate) {
                $q->whereDate('created_at', '<=', $endDate);
            });
        }
        $items = $query->latest('updated_at')->paginate(10)->withQueryString();
        $items->getCollection()->transform(function ($item) {
            $penerimaan = $item->transactions->where('jenis_transaksi', 'penerimaan')->sum('jumlah');
            $penyaluran = $item->transactions->where('jenis_transaksi', 'penyaluran')->sum('jumlah');
            $item->penerimaan_total = $penerimaan;
            $item->penyaluran_total = $penyaluran;
            $item->stok_akhir = $item->stok_awal + $penerimaan - $penyaluran;
            $item->tanggal_transaksi_terakhir = $item->transactions->max('created_at');
            return $item;
        });
        return view('dashboard_page.menu.data_pusat', [
            'items' => $items,
            'filters' => $request->only(['search', 'start_date', 'end_date'])
        ]);
    }

    /**
     * Menampilkan form untuk menambah data material baru.
     */
    public function create()
    {
        return view('dashboard_page.menu.tambah_material');
    }

    /**
     * Menyimpan data material baru ke database.
     */
    public function store(Request $request)
    {
        // ... (Kode tidak berubah)
        $validator = Validator::make($request->all(), [
            'nama_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')],
            'kode_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')],
            'total_stok' => 'required|integer|min:0',
        ], [
            'nama_material.unique' => 'Nama material ini sudah terdaftar di Pusat.',
            'kode_material.unique' => 'Kode material ini sudah terdaftar di Pusat.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            Item::create([
                'nama_material' => $request->nama_material,
                'kode_material' => $request->kode_material,
                'stok_awal' => $request->total_stok,
                'facility_id' => null,
                'region_id' => 1,
            ]);
            return response()->json(['success' => true, 'message' => 'Data material berhasil ditambahkan!', 'redirect_url' => route('pusat.index')], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * BARU: Mengambil data item spesifik untuk form edit.
     * Menggunakan Route Model Binding (Item $item).
     */
    public function edit(Item $item)
    {
        // Menghitung stok akhir untuk ditampilkan di modal
        $penerimaan = $item->transactions->where('jenis_transaksi', 'penerimaan')->sum('jumlah');
        $penyaluran = $item->transactions->where('jenis_transaksi', 'penyaluran')->sum('jumlah');
        $item->stok_akhir = $item->stok_awal + $penerimaan - $penyaluran;

        return response()->json($item);
    }

    /**
     * BARU: Memperbarui data material di database.
     */
    public function update(Request $request, Item $item)
    {
        // Validasi, pastikan nama & kode unik, tapi abaikan item yang sedang diedit
        $validator = Validator::make($request->all(), [
            'nama_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')->ignore($item->id)],
            'kode_material' => ['required', 'string', 'max:255', Rule::unique('items')->whereNull('facility_id')->ignore($item->id)],
            'stok_awal' => 'required|integer|min:0', // Mengedit stok awal
        ], [
            'nama_material.unique' => 'Nama material ini sudah digunakan.',
            'kode_material.unique' => 'Kode material ini sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update data
        $item->update([
            'nama_material' => $request->nama_material,
            'kode_material' => $request->kode_material,
            'stok_awal' => $request->stok_awal,
        ]);

        return response()->json(['success' => true, 'message' => 'Data material berhasil diperbarui!']);
    }

    /**
     * BARU: Menghapus data material dari database.
     */
    public function destroy(Item $item)
    {
        // Opsional: Tambahkan pengecekan jika item memiliki transaksi
        if ($item->transactions()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus! Material ini memiliki riwayat transaksi.'
            ], 409); // 409 Conflict
        }

        try {
            $item->delete();
            return response()->json(['success' => true, 'message' => 'Data material berhasil dihapus!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menghapus data.'], 500);
        }
    }
}