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
        $search = $request->input('search');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Menggunakan with('transactions') untuk Eager Loading standar
        $query = Item::whereNull('facility_id')->with('transactions');

        // Filter pencarian
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_material', 'like', "%{$search}%")
                    ->orWhere('kode_material', 'like', "%{$search}%");
            });
        }

        // ===================================================================
        // PERBAIKAN: Filter tanggal pada item berdasarkan transaksi yang dimilikinya.
        // Menggunakan 'created_at' sebagai tanggal transaksi yang sebenarnya.
        // ===================================================================
        if ($startDate) {
            $query->whereHas('transactions', function ($q) use ($startDate) {
                // Gunakan whereDate untuk membandingkan tanggal saja, tanpa jam
                $q->whereDate('created_at', '>=', $startDate);
            });
        }
        if ($endDate) {
            $query->whereHas('transactions', function ($q) use ($endDate) {
                $q->whereDate('created_at', '<=', $endDate);
            });
        }
        // ===================================================================

        $items = $query->latest('updated_at')->paginate(10)->withQueryString();

        // Setelah item difilter, kita hitung kalkulasinya
        $items->getCollection()->transform(function ($item) {
            // Jika tanggal difilter, kita juga harus filter transaksi di sini untuk kalkulasi yang akurat
            $transactionsInDateRange = $item->transactions;

            // Ambil filter tanggal dari URL untuk kalkulasi
            $startDate = request('start_date');
            $endDate = request('end_date');

            if ($startDate) {
                $transactionsInDateRange = $transactionsInDateRange->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
            }
            if ($endDate) {
                $transactionsInDateRange = $transactionsInDateRange->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
            }

            // PERBAIKAN: Kalkulasi sekarang menggunakan transaksi yang sudah terfilter
            $penerimaan = $transactionsInDateRange->where('jenis_transaksi', 'penerimaan')->sum('jumlah');
            $penyaluran = $transactionsInDateRange->where('jenis_transaksi', 'penyaluran')->sum('jumlah');

            $item->penerimaan_total = $penerimaan;
            $item->penyaluran_total = $penyaluran;

            // Stok akhir tetap dihitung dari semua transaksi, bukan hanya yang difilter
            $totalPenerimaan = $item->transactions->where('jenis_transaksi', 'penerimaan')->sum('jumlah');
            $totalPenyaluran = $item->transactions->where('jenis_transaksi', 'penyaluran')->sum('jumlah');
            $item->stok_akhir = $item->stok_awal + $totalPenerimaan - $totalPenyaluran;

            // PERBAIKAN: Gunakan 'created_at' untuk tanggal transaksi terakhir
            $item->tanggal_transaksi_terakhir = $item->transactions->max('created_at');
            return $item;
        });

        return view('dashboard_page.menu.data_pusat', [
            'items' => $items,
            'filters' => $request->only(['search', 'start_date', 'end_date'])
        ]);
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