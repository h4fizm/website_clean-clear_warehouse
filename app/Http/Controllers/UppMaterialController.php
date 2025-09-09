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
     * Menampilkan daftar UPP dari database dalam format tabel.
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

        // Filter dan pencarian data
        if ($request->filled('search')) {
            $query->where('no_surat_persetujuan', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->havingRaw('MIN(created_at) >= ?', [$startDate]);
        }
        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->havingRaw('MIN(created_at) <= ?', [$endDate]);
        }

        $query->orderByRaw('MIN(created_at) DESC');

        $upps = $query->paginate(5)->appends($request->all());

        return view('dashboard_page.menu.data_upp-material', compact('upps'));
    }

    /**
     * Mengambil data material afkir untuk ditampilkan di modal.
     */
    public function getMaterials()
    {
        $materials = Item::where('kategori_material', 'afkir')
            ->get()
            ->map(function ($item) {
                $lastTransaction = $item->transactions()->latest()->first();
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
     * Menampilkan form untuk menambah pengajuan UPP baru.
     */
    public function create()
    {
        return view('dashboard_page.upp_material.tambah_upp');
    }

    /**
     * Menyimpan pengajuan UPP baru ke database.
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

    /**
     * Menampilkan detail UPP untuk modal preview.
     */
    public function preview($no_surat)
    {
        $transactions = ItemTransaction::with('item')
            ->where('no_surat_persetujuan', $no_surat)
            ->where('jenis_transaksi', 'pemusnahan')
            ->get();

        if ($transactions->isEmpty()) {
            return response()->json(['error' => 'Data tidak ditemukan.'], 404);
        }

        $upp = [
            'no_surat' => $no_surat,
            'tgl_buat' => $transactions->min('created_at'),
            'tgl_update' => $transactions->max('updated_at'),
            'keterangan' => $transactions->first()->keterangan_transaksi,
            'materials' => $transactions,
        ];

        return view('dashboard_page.upp_material.modal_preview_upp', compact('upp'));
    }

    /**
     * Menampilkan form edit UPP dengan data yang sudah ada.
     */
    public function edit($no_surat)
    {
        $transactions = ItemTransaction::with('item', 'user')
            ->where('no_surat_persetujuan', $no_surat)
            ->where('jenis_transaksi', 'pemusnahan')
            ->get();

        if ($transactions->isEmpty()) {
            return redirect()->route('upp-material.index')->with('error', 'Data pengajuan UPP tidak ditemukan.');
        }

        $firstTransaction = $transactions->first();

        $upp = [
            'no_surat' => $no_surat,
            'tgl_buat' => $transactions->min('created_at'),
            'tgl_update' => $transactions->max('updated_at'),
            'keterangan' => $firstTransaction->keterangan_transaksi,
            'tanggal_pemusnahan' => $firstTransaction->tanggal_pemusnahan,
            'aktivitas_pemusnahan' => $firstTransaction->aktivitas_pemusnahan,
            'materials' => $transactions,
            'status' => $firstTransaction->status,
        ];

        $pjUser = $firstTransaction->user->name ?? '-';
        $tahapan = $firstTransaction->tahapan ?? '-';

        return view('dashboard_page.upp_material.preview_upp', compact('upp', 'pjUser', 'tahapan'));
    }

    /**
     * Memperbarui pengajuan UPP yang sudah ada.
     */
    public function update(Request $request, $no_surat)
    {
        // Validasi data yang diterima dari form
        $validator = Validator::make($request->all(), [
            'no_surat_baru' => 'required|string|max:255',
            'tanggal_pengajuan' => 'required|date',
            'tahapan' => 'required|string|max:255',
            'pj_user' => 'required|string|max:255',
            'tanggal_pemusnahan' => 'required|date',
            'aktivitas_pemusnahan' => 'required|string',
            'keterangan' => 'required|string',
            'materials' => 'required|array|min:1',
            'materials.*.item_id' => 'required|integer|exists:items,id',
            'materials.*.jumlah' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal. Pastikan semua field terisi dengan benar.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Cek duplikasi no_surat baru, kecuali jika sama dengan yang lama
            if ($request->no_surat_baru !== $no_surat) {
                $existingSurat = ItemTransaction::where('no_surat_persetujuan', $request->no_surat_baru)->first();
                if ($existingSurat) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nomor surat sudah digunakan. Silakan gunakan nomor lain.'
                    ], 409);
                }
            }

            // Kembalikan stok material dari transaksi yang akan dihapus
            $oldTransactions = ItemTransaction::where('no_surat_persetujuan', $no_surat)->where('jenis_transaksi', 'pemusnahan')->get();
            foreach ($oldTransactions as $oldTransaction) {
                $item = Item::find($oldTransaction->item_id);
                if ($item) {
                    $item->stok_akhir += $oldTransaction->jumlah;
                    $item->save();
                }
            }

            // Hapus transaksi lama yang terkait dengan no_surat ini
            ItemTransaction::where('no_surat_persetujuan', $no_surat)
                ->where('jenis_transaksi', 'pemusnahan')
                ->delete();

            $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();
            $userId = Auth::id();

            // Validasi stok di sisi server
            foreach ($request->materials as $materialData) {
                $item = Item::findOrFail($materialData['item_id']);
                if ($materialData['jumlah'] > $item->stok_akhir) {
                    throw new \Exception("Jumlah yang ingin dimusnahkan untuk {$item->nama_material} melebihi stok yang tersedia. Stok saat ini: {$item->stok_akhir} pcs.");
                }
            }

            foreach ($request->materials as $materialData) {
                $item = Item::findOrFail($materialData['item_id']);

                if (strtolower($item->kategori_material) !== 'afkir') {
                    throw new \Exception("Material {$item->nama_material} bukan kategori afkir.");
                }

                ItemTransaction::create([
                    'item_id' => $item->id,
                    'user_id' => $userId,
                    'region_from' => $pusatRegion->id,
                    'region_to' => $pusatRegion->id,
                    'jumlah' => $materialData['jumlah'],
                    'stok_awal_asal' => $item->stok_akhir,
                    'stok_akhir_asal' => $item->stok_akhir - $materialData['jumlah'],
                    'jenis_transaksi' => 'pemusnahan',
                    'no_surat_persetujuan' => $request->no_surat_baru,
                    'keterangan_transaksi' => $request->keterangan,
                    'tanggal_pemusnahan' => $request->tanggal_pemusnahan,
                    'aktivitas_pemusnahan' => $request->aktivitas_pemusnahan,
                    'tahapan' => $request->tahapan, // Mengambil dari form
                    'status' => 'proses',
                    'created_at' => Carbon::parse($request->tanggal_pengajuan), // Menyimpan tanggal pengajuan yang diedit
                ]);

                // Kurangi stok akhir item karena proses pemusnahan dianggap selesai
                $item->stok_akhir -= $materialData['jumlah'];
                $item->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan UPP berhasil diperbarui. Status tetap dalam proses.',
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

    /**
     * Metode baru untuk mengubah status UPP.
     */
    public function changeStatus(Request $request, $no_surat)
    {
        // Validasi input
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:proses,done',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Status tidak valid.'], 400);
        }

        try {
            DB::beginTransaction();

            $updated = ItemTransaction::where('no_surat_persetujuan', $no_surat)
                ->where('jenis_transaksi', 'pemusnahan')
                ->update([
                    'status' => $request->status,
                    'updated_at' => Carbon::now() // Memperbarui timestamp
                ]);

            if ($updated) {
                DB::commit();
                return response()->json(['message' => "Status berhasil diubah menjadi **{$request->status}**."]);
            }

            DB::rollBack();
            return response()->json(['message' => 'Data tidak ditemukan atau tidak ada perubahan.'], 404);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mengubah status: ' . $e->getMessage()], 500);
        }
    }
}