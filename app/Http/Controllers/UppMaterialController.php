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
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UppMaterialExport;
use Illuminate\Validation\ValidationException;

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
            ->select('id', 'nama_material', 'kode_material', 'stok_akhir')
            ->get();

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

                if (strtolower($item->kategori_material) !== 'afkir') {
                    throw new \Exception("Material {$item->nama_material} bukan kategori afkir.");
                }

                ItemTransaction::create([
                    'item_id' => $item->id,
                    'user_id' => $userId,
                    'region_from' => $pusatRegion->id,
                    'region_to' => $pusatRegion->id,
                    'jumlah' => $materialData['jumlah_diambil'],
                    'jenis_transaksi' => 'pemusnahan',
                    'no_surat_persetujuan' => $request->noSurat,
                    'keterangan_transaksi' => $request->keterangan,
                    'tahapan' => $request->tahapan,
                    'status' => 'proses',
                    'created_at' => Carbon::parse($request->tanggal),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan UPP berhasil ditambahkan. Status dalam proses.',
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

        // Perbaikan: Ambil stok akhir saat ini dari item
        $materialsWithCurrentStock = $transactions->map(function ($transaction) {
            $item = Item::find($transaction->item_id);
            $currentStock = $item->stok_akhir ?? 0;
            return [
                'id' => $transaction->item->id,
                'nama_material' => $transaction->item->nama_material,
                'kode_material' => $transaction->item->kode_material,
                'stok_akhir_pusat' => $currentStock,
                'jumlah_diajukan' => $transaction->jumlah,
            ];
        });

        $upp = [
            'no_surat' => $no_surat,
            'tgl_buat' => $transactions->min('created_at'),
            'tgl_update' => $transactions->max('updated_at'),
            'keterangan' => $firstTransaction->keterangan_transaksi,
            'tanggal_pemusnahan' => $firstTransaction->tanggal_pemusnahan,
            'aktivitas_pemusnahan' => $firstTransaction->aktivitas_pemusnahan,
            'materials' => $materialsWithCurrentStock,
            'status' => $firstTransaction->status,
        ];

        $pjUser = $firstTransaction->user->name ?? '';   // kosongkan kalau null
        $tahapan = $firstTransaction->tahapan ?? '';     // kosongkan kalau null

        return view('dashboard_page.upp_material.preview_upp', compact('upp', 'pjUser', 'tahapan'));

    }

    /**
     * Memperbarui pengajuan UPP yang sudah ada.
     */
    public function update(Request $request, $no_surat)
    {
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

            if ($request->no_surat_baru !== $no_surat) {
                $existingSurat = ItemTransaction::where('no_surat_persetujuan', $request->no_surat_baru)->first();
                if ($existingSurat) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nomor surat sudah digunakan. Silakan gunakan nomor lain.'
                    ], 409);
                }
            }

            ItemTransaction::where('no_surat_persetujuan', $no_surat)
                ->where('jenis_transaksi', 'pemusnahan')
                ->delete();

            $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();
            $userId = Auth::id();

            foreach ($request->materials as $materialData) {
                $item = Item::findOrFail($materialData['item_id']);

                if (strtolower($item->kategori_material) !== 'afkir') {
                    throw new \Exception("Material {$item->nama_material} bukan kategori afkir.");
                }

                $stokAwalAsal = $item->stok_akhir;
                $stokAkhirAsal = $stokAwalAsal - $materialData['jumlah'];

                ItemTransaction::create([
                    'item_id' => $item->id,
                    'pj_user' => $request->pjUser,
                    'region_from' => $pusatRegion->id,
                    'region_to' => $pusatRegion->id,
                    'jumlah' => $materialData['jumlah'],
                    'stok_awal_asal' => $stokAwalAsal,
                    'stok_akhir_asal' => $stokAkhirAsal,
                    'jenis_transaksi' => 'pemusnahan',
                    'no_surat_persetujuan' => $request->no_surat_baru,
                    'keterangan_transaksi' => $request->keterangan,
                    'tanggal_pemusnahan' => $request->tanggal_pemusnahan,
                    'aktivitas_pemusnahan' => $request->aktivitas_pemusnahan,
                    'tahapan' => $request->tahapan,
                    'status' => 'proses',
                    'created_at' => Carbon::parse($request->tanggal_pengajuan),
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan UPP berhasil diperbarui dan stok universal telah disesuaikan.',
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
     * Metode untuk mengubah status UPP.
     */
    public function changeStatus(Request $request, $no_surat)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:proses,done',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Status tidak valid.'], 400);
        }

        try {
            DB::beginTransaction();

            $transactions = ItemTransaction::with('item')
                ->where('no_surat_persetujuan', $no_surat)
                ->where('jenis_transaksi', 'pemusnahan')
                ->get();

            if ($transactions->isEmpty()) {
                DB::rollBack();
                return response()->json(['message' => 'Data pengajuan tidak ditemukan.'], 404);
            }

            $currentStatus = strtolower($transactions->first()->status);
            $newStatus = strtolower($request->status);

            if ($currentStatus === $newStatus) {
                DB::rollBack();
                return response()->json(['message' => "Status sudah **{$newStatus}**. Tidak ada perubahan yang dilakukan."]);
            }

            if ($newStatus === 'done') {
                $this->reduceStock($transactions);
            } else if ($newStatus === 'proses') {
                $this->restoreStock($transactions);
            }

            ItemTransaction::where('no_surat_persetujuan', $no_surat)
                ->where('jenis_transaksi', 'pemusnahan')
                ->update([
                    'status' => $newStatus,
                    'updated_at' => Carbon::now()
                ]);

            DB::commit();
            return response()->json(['message' => "Status berhasil diubah menjadi **{$newStatus}** dan stok universal telah diperbarui."]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal mengubah status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengurangi stok dari koleksi transaksi.
     * @param \Illuminate\Support\Collection $transactions
     * @throws \Exception
     */
    private function reduceStock($transactions)
    {
        foreach ($transactions as $transaction) {
            $item = Item::find($transaction->item_id);
            if (!$item) {
                throw new \Exception("Material dengan ID {$transaction->item_id} tidak ditemukan.");
            }
            if ($item->stok_akhir < $transaction->jumlah) {
                throw new \Exception("Stok {$item->nama_material} tidak mencukupi untuk pemusnahan.");
            }
            $item->decrement('stok_akhir', $transaction->jumlah);
        }
    }

    /**
     * Mengembalikan stok dari koleksi transaksi.
     * @param \Illuminate\Support\Collection $transactions
     */
    private function restoreStock($transactions)
    {
        foreach ($transactions as $transaction) {
            $item = Item::find($transaction->item_id);
            if ($item) {
                $item->increment('stok_akhir', $transaction->jumlah);
            }
        }
    }

    /**
     * Metode baru untuk ekspor data UPP material ke Excel.
     */
    public function exportExcel(Request $request)
    {
        $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $filters = [
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        $startDate = $filters['start_date'] ? Carbon::parse($filters['start_date'])->format('d-m-Y') : 'Awal';
        $endDate = $filters['end_date'] ? Carbon::parse($filters['end_date'])->format('d-m-Y') : 'Akhir';
        $filename = "Laporan UPP Material ({$startDate} - {$endDate}).xlsx";

        return Excel::download(new UppMaterialExport($filters), $filename);
    }
}