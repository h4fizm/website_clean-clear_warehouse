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
            ->where('no_surat_persetujuan', 'NOT LIKE', '[DELETED_%')
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
     * Mengambil data material afkir yang hanya berada di pusat.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMaterials()
    {
        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->first();

        if (!$pusatRegion) {
            return response()->json([]);
        }

        $materials = Item::where('kategori_material', 'afkir')
            ->where('region_id', $pusatRegion->id)
            ->where('stok_akhir', '>', 0)
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
            'penanggungjawab' => 'required|string|max:255',
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
            $stockErrors = [];

            foreach ($request->materials as $materialData) {
                $item = Item::find($materialData['id']);

                if (!$item || strtolower($item->kategori_material) !== 'afkir') {
                    $stockErrors[] = "Material {$item->nama_material} tidak valid atau bukan kategori afkir.";
                    continue;
                }

                if ($item->region_id !== $pusatRegion->id) {
                    $stockErrors[] = "Material {$item->nama_material} tidak berada di pusat.";
                    continue;
                }

                if ($item->stok_akhir < $materialData['jumlah_diambil']) {
                    $stockErrors[] = "Stok material '{$item->nama_material}' tidak mencukupi. Stok tersedia: {$item->stok_akhir} pcs.";
                } else {
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
                        'penanggungjawab' => $request->penanggungjawab,
                        'status' => 'proses',
                        'created_at' => Carbon::parse($request->tanggal),
                    ]);
                }
            }

            if (!empty($stockErrors)) {
                throw new \Exception(implode("<br>", $stockErrors));
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
                'message' => 'Gagal menyimpan pengajuan: ' . $e->getMessage()
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
        $transactions = ItemTransaction::with('item')
            ->where('no_surat_persetujuan', $no_surat)
            ->where('jenis_transaksi', 'pemusnahan')
            ->get();

        if ($transactions->isEmpty()) {
            return redirect()->route('upp-material.index')->with('error', 'Data pengajuan UPP tidak ditemukan.');
        }

        $firstTransaction = $transactions->first();

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
            'penanggungjawab' => $firstTransaction->penanggungjawab,
            'materials' => $materialsWithCurrentStock,
            'status' => $firstTransaction->status,
        ];

        $tahapan = $firstTransaction->tahapan ?? '';

        return view('dashboard_page.upp_material.preview_upp', compact('upp', 'tahapan'));
    }

    /**
     * Memperbarui pengajuan UPP yang sudah ada.
     * Metode ini hanya meng-update data, bukan status atau stok.
     */
    public function update(Request $request, $no_surat)
    {
        $validator = Validator::make($request->all(), [
            'no_surat_baru' => 'required|string|max:255',
            'tanggal_pengajuan' => 'required|date',
            'tahapan' => 'required|string|max:255',
            'penanggungjawab' => 'required|string|max:255',
            'tanggal_pemusnahan' => 'nullable|date',
            'aktivitas_pemusnahan' => 'nullable|string',
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

            // Cek apakah ada perubahan nomor surat dan pastikan nomor baru tidak ada di database
            if ($request->no_surat_baru !== $no_surat) {
                $existingSurat = ItemTransaction::where('no_surat_persetujuan', $request->no_surat_baru)->first();
                if ($existingSurat) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Nomor surat sudah digunakan. Silakan gunakan nomor lain.'
                    ], 409);
                }
            }

            // Dapatkan status saat ini untuk dipertahankan setelah update
            $currentStatus = ItemTransaction::where('no_surat_persetujuan', $no_surat)
                ->first()
                ->status ?? 'proses';

            // Hapus semua transaksi lama yang terkait dengan nomor surat ini
            ItemTransaction::where('no_surat_persetujuan', $no_surat)
                ->where('jenis_transaksi', 'pemusnahan')
                ->delete();

            $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();
            $userId = Auth::id();

            foreach ($request->materials as $materialData) {
                $item = Item::findOrFail($materialData['item_id']);

                // Pengecekan stok saat melakukan pembaruan jika status sudah "done"
                if ($currentStatus === 'done' && $item->stok_akhir < $materialData['jumlah']) {
                    throw new \Exception("Stok material '{$item->nama_material}' tidak mencukupi untuk pembaruan. Stok tersedia: {$item->stok_akhir}.");
                }

                ItemTransaction::create([
                    'item_id' => $item->id,
                    'user_id' => $userId,
                    'penanggungjawab' => $request->penanggungjawab,
                    'region_from' => $pusatRegion->id,
                    'region_to' => $pusatRegion->id,
                    'jumlah' => $materialData['jumlah'],
                    'jenis_transaksi' => 'pemusnahan',
                    'no_surat_persetujuan' => $request->no_surat_baru,
                    'keterangan_transaksi' => $request->keterangan,
                    'tanggal_pemusnahan' => $request->tanggal_pemusnahan,
                    'aktivitas_pemusnahan' => $request->aktivitas_pemusnahan,
                    'tahapan' => $request->tahapan,
                    'status' => $currentStatus,
                    'created_at' => Carbon::parse($request->tanggal_pengajuan),
                ]);
            }

            // Jika status sudah "done" dan tidak ada kesalahan stok, kurangi stok
            if ($currentStatus === 'done') {
                $this->reduceStock(ItemTransaction::where('no_surat_persetujuan', $request->no_surat_baru)->get());
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan UPP berhasil diperbarui.',
                'redirect' => route('upp-material.edit', $request->no_surat_baru)
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui pengajuan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Metode untuk mengubah status UPP dan memproses stok.
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
                ->lockForUpdate() // Tambahkan penguncian baris di sini
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

            // Logika utama:
            if ($newStatus === 'done' && $currentStatus === 'proses') {
                // Kurangi stok jika status berubah dari proses ke done
                $this->reduceStock($transactions);
            } else if ($newStatus === 'proses' && $currentStatus === 'done') {
                // Kembalikan stok jika status berubah dari done ke proses
                $this->restoreStock($transactions);
            }

            // Perbarui status semua transaksi yang terkait
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
        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->first();
        if (!$pusatRegion) {
            throw new \Exception("Region 'P.Layang (Pusat)' tidak ditemukan.");
        }

        foreach ($transactions as $transaction) {
            $item = Item::where('id', $transaction->item_id)
                ->lockForUpdate() // Kunci item juga saat diupdate
                ->first();

            if (!$item) {
                throw new \Exception("Material dengan ID {$transaction->item_id} tidak ditemukan.");
            }

            if ($item->region_id !== $pusatRegion->id || strtolower($item->kategori_material) !== 'afkir') {
                throw new \Exception("Material {$item->nama_material} tidak valid untuk pemusnahan.");
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
        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->first();
        if (!$pusatRegion) {
            // Jika region pusat tidak ditemukan, jangan lakukan apa-apa, atau lempar exception
            return;
        }

        foreach ($transactions as $transaction) {
            $item = Item::where('id', $transaction->item_id)
                ->lockForUpdate()
                ->first();

            if ($item && $item->region_id === $pusatRegion->id && strtolower($item->kategori_material) === 'afkir') {
                $item->increment('stok_akhir', $transaction->jumlah);
            }
        }
    }

    /**
     * Menghapus pengajuan UPP secara permanen dari database.
     */
    public function destroy($no_surat)
    {
        try {
            DB::transaction(function () use ($no_surat) {
                // Cari semua transaksi terkait nomor surat ini
                $transactions = ItemTransaction::where('no_surat_persetujuan', $no_surat)
                    ->where('jenis_transaksi', 'pemusnahan')
                    ->get();

                if ($transactions->isEmpty()) {
                    throw new \Exception("Data pengajuan UPP dengan nomor surat '{$no_surat}' tidak ditemukan.");
                }

                // UPDATE: Tandai transaksi dengan menambahkan prefix pada no_surat_persetujuan
                // Ini akan menghapusnya dari daftar UPP tapi tetap menjaga history
                $markedSurat = "[DELETED_" . now()->timestamp . "]_" . $no_surat;

                ItemTransaction::where('no_surat_persetujuan', $no_surat)
                    ->where('jenis_transaksi', 'pemusnahan')
                    ->update([
                        'no_surat_persetujuan' => $markedSurat,
                        'keterangan_transaksi' => DB::raw("CONCAT(keterangan_transaksi, ' [PENGHAJUAN UPP DIHAPUS PADA: ', NOW(), ')')")
                    ]);
            });

            // Tentukan redirect berdasarkan parameter atau default ke UPP Material
            $redirectTo = request()->input('redirect_to', 'upp-material');
            $redirectRoute = $redirectTo === 'dashboard' ? 'dashboard' : 'upp-material.index';

            return redirect()->route($redirectRoute)->with('success', "Pengajuan UPP '{$no_surat}' berhasil dihapus. Stok material tetap berkurang dan history pemusnahan tetap tercatat di tabel pusat.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Gagal menghapus pengajuan UPP: " . $e->getMessage());
        }
    }

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