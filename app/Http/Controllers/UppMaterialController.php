<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Region;
use App\Models\ItemTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UppMaterialController extends Controller
{
    /**
     * List data UPP dari database → tampil di view tabel.
     */
    public function index(Request $request)
    {
        $query = ItemTransaction::select(
            'no_surat_persetujuan',
            DB::raw('MIN(created_at) as tgl_buat'),
            DB::raw('MAX(updated_at) as tgl_update'),
            DB::raw('MAX(tahapan) as tahapan'),
            DB::raw('MAX(status) as status')
        )
            ->where('jenis_transaksi', 'pemusnahan')
            ->groupBy('no_surat_persetujuan');

        // 🔍 Search
        if ($request->filled('search')) {
            $query->where('no_surat_persetujuan', 'like', '%' . $request->search . '%');
        }

        // 📅 Filter tanggal (Perbaikan ada di sini)
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->havingRaw('MIN(created_at) >= ?', [$startDate]);
        }
        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->havingRaw('MIN(created_at) <= ?', [$endDate]);
        }

        // Atur pengurutan agar data terbaru muncul di atas
        $query->orderByRaw('MIN(created_at) DESC');

        $upps = $query->paginate(5)->appends($request->all());

        return view('dashboard_page.menu.data_upp-material', compact('upps'));
    }

    public function getMaterials()
    {
        // Ambil semua material dengan kategori 'afkir'
        $materials = Item::where('kategori_material', 'afkir')
            ->get()
            ->map(function ($item) {
                // Cari transaksi terakhir untuk material ini
                $lastTransaction = $item->transactions()->latest()->first();

                // Tentukan nilai stok yang akan ditampilkan
                // Jika ada transaksi, gunakan stok_akhir_asal dari transaksi terakhir
                // Jika tidak ada transaksi, gunakan stok_awal dari tabel item
                $stokToShow = $lastTransaction ? $lastTransaction->stok_akhir_asal : ($item->stok_awal ?? 0);

                return [
                    'id' => $item->id,
                    'nama_material' => $item->nama_material,
                    'kode_material' => $item->kode_material,
                    'stok_akhir' => $stokToShow,
                ];
            });

        return response()->json($materials);
    }

    /**
     * Form tambah UPP baru.
     */
    public function create()
    {
        // Data material afkir sudah diambil lewat AJAX di frontend
        return view('dashboard_page.upp_material.tambah_upp');
    }

    /**
     * Simpan UPP baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noSurat' => 'required|string|max:255|unique:item_transactions,no_surat_persetujuan',
            'tanggal' => 'required|date',
            'tahapan' => 'required|string|max:255',
            'pjUser' => 'required|string|max:255',
            'keterangan' => 'required|string',
            'materials' => 'required|array|min:1',
            'materials.*.id' => 'required|integer|exists:items,id',
            'materials.*.jumlah_diambil' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();
            $userId = Auth::id();

            foreach ($request->materials as $materialData) {
                $item = Item::findOrFail($materialData['id']);

                if ($materialData['jumlah_diambil'] > $item->stok_akhir) {
                    throw new \Exception("Jumlah yang diajukan untuk {$item->nama_material} melebihi stok yang tersedia. Stok saat ini: {$item->stok_akhir} pcs.");
                }

                if (strtolower($item->kategori_material) !== 'afkir') {
                    throw new \Exception("Material {$item->nama_material} bukan kategori afkir.");
                }

                ItemTransaction::create([
                    'item_id' => $item->id,
                    'user_id' => $userId,
                    'region_from' => $pusatRegion->id,
                    'region_to' => $pusatRegion->id,
                    'jumlah' => $materialData['jumlah_diambil'],
                    'stok_awal_asal' => $item->stok_akhir,
                    'stok_akhir_asal' => $item->stok_akhir - $materialData['jumlah_diambil'],
                    'jenis_transaksi' => 'pemusnahan',
                    'no_surat_persetujuan' => $request->noSurat,
                    'keterangan_transaksi' => $request->keterangan,
                    'tahapan' => $request->tahapan,
                    'status' => 'proses',
                    'created_at' => Carbon::parse($request->tanggal),
                ]);

                $item->stok_akhir -= $materialData['jumlah_diambil'];
                $item->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan UPP berhasil ditambahkan.',
                'redirect' => route('upp-material.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function preview($no_surat)
    {
        $transactions = ItemTransaction::with('item')
            ->where('no_surat_persetujuan', $no_surat)
            ->where('jenis_transaksi', 'pemusnahan')
            ->get();

        if ($transactions->isEmpty()) {
            // Mengembalikan respons JSON dengan status 404 jika data tidak ditemukan
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }

        $upp = [
            'no_surat' => $no_surat,
            'tgl_buat' => $transactions->min('created_at'),
            'tgl_update' => $transactions->max('updated_at'),
            'keterangan' => $transactions->first()->keterangan_transaksi,
            'materials' => $transactions,
        ];

        // Mengembalikan view modal sebagai string HTML
        return view('dashboard_page.upp_material.modal_preview_upp', compact('upp'));
    }
}