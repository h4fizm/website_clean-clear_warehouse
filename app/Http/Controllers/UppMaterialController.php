<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Region;
use App\Models\ItemTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
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

        // 📅 Filter tanggal
        if ($request->filled('start_date')) {
            $query->havingRaw('MIN(created_at) >= ?', [$request->start_date]);
        }
        if ($request->filled('end_date')) {
            $query->havingRaw('MIN(created_at) <= ?', [$request->end_date]);
        }

        $upps = $query->paginate(10)->appends($request->all());

        return view('dashboard_page.menu.data_upp-material', compact('upps'));
    }

    public function getMaterials()
    {
        $materials = Item::select('id', 'nama_material', 'kode_material', 'stok_akhir')
            ->where('kategori_material', 'afkir')
            ->get()
            ->map(function ($item) {
                // Ambil transaksi terakhir untuk item ini
                $lastTransaction = $item->transactions()
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Tentukan stok akhir (fallback ke item->stok_akhir atau 0 kalau null)
                $stokAkhir = $lastTransaction
                    ? $lastTransaction->stok_akhir_asal
                    : ($item->stok_akhir ?? 0);

                return [
                    'id' => $item->id,
                    'nama_material' => $item->nama_material,
                    'kode_material' => $item->kode_material,
                    'stok_akhir' => $stokAkhir,
                ];
            });

        return response()->json($materials);
    }



    /**
     * Form tambah UPP baru.
     */
    public function create()
    {
        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();

        // hanya material afkir di pusat
        $materials = Item::where('kategori_material', 'afkir')
            ->where('region_id', $pusatRegion->id)
            ->whereNull('facility_id')
            ->get();

        return view('dashboard_page.upp_material.tambah_upp', compact('materials'));
    }

    /**
     * Simpan UPP baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'noSurat' => 'required|string|max:255',
            'tanggal' => 'required|date',
            'tahapan' => 'required|string|max:255',
            'pjUser' => 'required|string|max:255',
            'keterangan' => 'required|string', // sekarang wajib
            'materials' => 'required|array|min:1',
            'materials.*.id' => 'required|integer|exists:items,id',
            'materials.*.jumlah_diambil' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();
            $userId = Auth::id();

            foreach ($request->materials as $materialData) {
                $item = Item::findOrFail($materialData['id']);

                // validasi stok
                if ($materialData['jumlah_diambil'] > $item->stok_akhir) {
                    throw new \Exception("Jumlah yang diajukan untuk {$item->nama_material} melebihi stok yang tersedia.");
                }

                // cek kategori harus afkir
                if (strtolower($item->kategori_material) !== 'afkir') {
                    throw new \Exception("Material {$item->nama_material} bukan kategori afkir.");
                }

                // simpan transaksi
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
                    'tahapan' => $request->tahapan,   // ✅ Tambahkan ini
                    'status' => 'proses',             // ✅ Default status awal
                    'created_at' => Carbon::parse($request->tanggal),
                ]);


                // update stok
                $item->stok_akhir -= $materialData['jumlah_diambil'];
                $item->save();
            }

            DB::commit();

            return redirect()->route('upp-material.index')->with('success', 'Pengajuan UPP berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Preview detail UPP berdasarkan no_surat.
     */
    public function preview($no_surat)
    {
        $transactions = ItemTransaction::with('item')
            ->where('no_surat_persetujuan', $no_surat)
            ->where('jenis_transaksi', 'pemusnahan')
            ->get();

        if ($transactions->isEmpty()) {
            return redirect()->route('upp-material.index')->with('error', 'Data tidak ditemukan.');
        }

        $upp = [
            'no_surat' => $no_surat,
            'tgl_buat' => $transactions->min('created_at'),
            'tgl_update' => $transactions->max('updated_at'),
            'keterangan' => $transactions->first()->keterangan_transaksi,
            'materials' => $transactions,
        ];

        return view('dashboard_page.upp_material.preview_upp', compact('upp'));
    }
}
