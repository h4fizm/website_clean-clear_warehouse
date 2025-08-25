<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\Item;
use App\Models\Facility;
use App\Models\ItemTransaction;
use Carbon\Carbon;

class PusatController extends Controller
{
    /**
     * Menampilkan data item P.Layang (Pusat) dan daftar facility untuk dropdown.
     */
    public function index(Request $request)
    {
        // Ambil semua input filter dari request
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        $query = Item::query();
        $query->whereNotNull('region_id')->whereNull('facility_id');

        // Filter berdasarkan pencarian nama material
        $query->when($filters['search'], function ($q, $search) {
            return $q->where('nama_material', 'like', '%' . $search . '%');
        });

        // Filter berdasarkan tanggal
        $query->when($filters['start_date'], function ($q, $startDate) {
            return $q->whereDate('updated_at', '>=', $startDate);
        });
        $query->when($filters['end_date'], function ($q, $endDate) {
            return $q->whereDate('updated_at', '<=', $endDate);
        });

        // [INI BAGIAN YANG DITAMBAHKAN]
        // Menghitung total penerimaan untuk setiap item
        $query->withSum([
            'transactions as penerimaan_total' => function ($q) {
                $q->where('jenis_transaksi', 'penerimaan');
            }
        ], 'jumlah');

        // Menghitung total penyaluran untuk setiap item
        $query->withSum([
            'transactions as penyaluran_total' => function ($q) {
                $q->where('jenis_transaksi', 'penyaluran');
            }
        ], 'jumlah');

        // [INI PERBAIKANNYA]
        // Mengambil tanggal transaksi paling baru untuk setiap item
        $query->withMax('transactions as latest_transaction_date', 'created_at');

        // Eksekusi query dengan pagination
        $items = $query->latest('updated_at')->paginate(10);

        // Ambil data facility untuk dropdown
        $facilities = Facility::orderBy('name')->get();

        // Kirim semua data ke view
        return view('dashboard_page.menu.data_pusat', compact('items', 'filters', 'facilities'));
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
            'nama_material' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items')->whereNull('facility_id')->ignore($item->id),
            ],
            'kode_material' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items')->whereNull('facility_id')->ignore($item->id),
            ],
            'stok_awal' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_item_id', $item->id);
        }

        // simpan kode lama dulu
        $oldKode = $item->kode_material;

        // update pusat
        $item->update($request->only(['nama_material', 'kode_material', 'stok_awal']));

        // sinkronkan ke semua cabang (hanya nama & kode)
        Item::whereNotNull('facility_id')
            ->where('kode_material', $oldKode)
            ->update([
                'nama_material' => $item->nama_material,
                'kode_material' => $item->kode_material,
            ]);

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

    public function transfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id_pusat' => 'required|exists:items,id',
            'facility_id_selected' => 'required|exists:facilities,id',
            'kode_material' => 'required|string',
            'jenis_transaksi' => 'required|in:penyaluran,penerimaan',
            'jumlah' => 'required|integer|min:1',
            'tanggal_transaksi' => 'required|date',
            'no_surat_persetujuan' => 'nullable|string',
            'no_ba_serah_terima' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $response = DB::transaction(function () use ($request) {
                $jenis_transaksi = $request->jenis_transaksi;
                $jumlah = $request->jumlah;

                // --- LOGIKA PENYALURAN (DARI PUSAT KE SPBE/BPT) ---
                if ($jenis_transaksi == 'penyaluran') {
                    $itemFrom = Item::findOrFail($request->item_id_pusat); // Item dari Pusat
                    $facilityToId = $request->facility_id_selected;

                    if ($itemFrom->stok_akhir < $jumlah) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['jumlah' => 'Stok di P.Layang tidak mencukupi!']);
                    }

                    // Ciptakan item di tujuan jika belum ada
                    Item::firstOrCreate(
                        ['facility_id' => $facilityToId, 'kode_material' => $itemFrom->kode_material],
                        ['nama_material' => $itemFrom->nama_material, 'stok_awal' => 0]
                    );

                    // HANYA BUAT SATU TRANSAKSI
                    ItemTransaction::create([
                        'item_id' => $itemFrom->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'transfer', // Gunakan jenis 'transfer' agar lebih jelas
                        'jumlah' => $jumlah,
                        'region_from' => $itemFrom->region_id, // ASAL: PUSAT
                        'facility_to' => $facilityToId,        // TUJUAN: SPBE/BPT
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => \Carbon\Carbon::parse($request->tanggal_transaksi),
                        'updated_at' => \Carbon\Carbon::parse($request->tanggal_transaksi),
                    ]);

                    return ['success' => true, 'message' => 'Transfer material berhasil dicatat!'];
                }

                // --- LOGIKA PENERIMAAN (DARI SPBE/BPT KE PUSAT) ---
                if ($jenis_transaksi == 'penerimaan') {
                    $facilityFromId = $request->facility_id_selected;
                    $itemTo = Item::findOrFail($request->item_id_pusat); // Item Tujuan (Pusat)

                    $itemFrom = Item::where('facility_id', $facilityFromId)
                        ->where('kode_material', $request->kode_material)
                        ->first();

                    if (!$itemFrom || $itemFrom->stok_akhir < $jumlah) {
                        throw \Illuminate\Validation\ValidationException::withMessages(['jumlah' => 'Stok di SPBE/BPT asal tidak ada atau tidak mencukupi!']);
                    }

                    // HANYA BUAT SATU TRANSAKSI
                    ItemTransaction::create([
                        'item_id' => $itemFrom->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'transfer',
                        'jumlah' => $jumlah,
                        'facility_from' => $facilityFromId, // ASAL: SPBE/BPT
                        'region_to' => $itemTo->region_id,    // TUJUAN: PUSAT
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => \Carbon\Carbon::parse($request->tanggal_transaksi),
                        'updated_at' => \Carbon\Carbon::parse($request->tanggal_transaksi),
                    ]);

                    return ['success' => true, 'message' => 'Transfer material berhasil dicatat!'];
                }
            });

            return response()->json($response);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}