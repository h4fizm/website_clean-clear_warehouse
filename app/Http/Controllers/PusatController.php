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
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        $query = Item::query()
            ->whereNull('facility_id')
            ->select('items.*');

        // Filter pencarian berdasarkan nama atau kode material
        $query->when($filters['search'], function ($q, $search) {
            return $q->where(function ($subQ) use ($search) {
                $subQ->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });

        // Filter daftar item utama berdasarkan rentang tanggal transaksi.
        $query->when($filters['start_date'] || $filters['end_date'], function ($q) use ($filters) {
            $q->whereHas('transactions', function ($subQuery) use ($filters) {
                if ($filters['start_date']) {
                    $subQuery->whereDate('created_at', '>=', $filters['start_date']);
                }
                if ($filters['end_date']) {
                    $subQuery->whereDate('created_at', '<=', $filters['end_date']);
                }
            });
        });

        // [FIX] Logika subquery diubah agar menargetkan item_id secara spesifik
        // dan membedakan transaksi masuk (penerimaan) dan keluar (penyaluran).
        $query->addSelect([
            // PENERIMAAN: Transaksi yang DITUJUKAN ke item ini (region_to diisi)
            'penerimaan_total' => ItemTransaction::selectRaw('COALESCE(sum(jumlah), 0)')
                ->whereColumn('item_id', 'items.id') // <-- Kunci: Targetkan item_id spesifik
                ->whereNotNull('region_to') // <-- Ciri khas transaksi penerimaan ke Pusat
                ->when($filters['start_date'], function ($subQuery, $date) {
                    $subQuery->whereDate('created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($subQuery, $date) {
                    $subQuery->whereDate('created_at', '<=', $date);
                }),

            // PENYALURAN: Transaksi yang BERASAL dari item ini (region_from diisi)
            'penyaluran_total' => ItemTransaction::selectRaw('COALESCE(sum(jumlah), 0)')
                ->whereColumn('item_id', 'items.id') // <-- Kunci: Targetkan item_id spesifik
                ->whereNotNull('region_from') // <-- Ciri khas transaksi penyaluran dari Pusat
                ->when($filters['start_date'], function ($subQuery, $date) {
                    $subQuery->whereDate('created_at', '>=', $date);
                })
                ->when($filters['end_date'], function ($subQuery, $date) {
                    $subQuery->whereDate('created_at', '<=', $date);
                }),
        ]);

        $query->withMax('transactions as latest_transaction_date', 'created_at');

        $items = $query->latest('updated_at')->paginate(10)->withQueryString();
        $facilities = Facility::orderBy('name')->get();

        return view('dashboard_page.menu.data_pusat', compact('items', 'filters', 'facilities'));
    }

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
        // 1. Tambahkan validasi untuk stok_awal
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
            // Aturan validasi untuk stok awal
            'stok_awal' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_item_id', $item->id);
        }

        $oldKode = $item->kode_material;

        // 2. Hitung total penerimaan dan penyaluran untuk item ini dari seluruh riwayat transaksi
        // Ini diperlukan untuk menghitung ulang stok_akhir secara akurat
        $totalPenerimaan = $item->transactions()
            ->whereNotNull('region_to') // Ciri khas penerimaan ke Pusat
            ->sum('jumlah');

        $totalPenyaluran = $item->transactions()
            ->whereNotNull('region_from') // Ciri khas penyaluran dari Pusat
            ->sum('jumlah');

        // 3. Ambil nilai stok awal baru dari request
        $stokAwalBaru = $request->stok_awal;

        // 4. Hitung stok akhir baru berdasarkan rumus:
        // Stok Akhir = Stok Awal Baru + Total Penerimaan - Total Penyaluran
        $stokAkhirBaru = $stokAwalBaru + $totalPenerimaan - $totalPenyaluran;

        // 5. Update semua field yang relevan dalam satu operasi
        $item->update([
            'nama_material' => $request->nama_material,
            'kode_material' => $request->kode_material,
            'stok_awal' => $stokAwalBaru,
            'stok_akhir' => $stokAkhirBaru, // Simpan stok akhir yang sudah dihitung ulang
        ]);

        // Sinkronisasi nama dan kode material ke cabang tetap berjalan
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

                if ($jenis_transaksi == 'penyaluran') {
                    $itemFrom = Item::findOrFail($request->item_id_pusat);
                    if ($itemFrom->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => 'Stok di P.Layang tidak mencukupi!']);
                    }

                    $stokAwalAsal = $itemFrom->stok_akhir;
                    $stokAkhirAsal = $stokAwalAsal - $jumlah;

                    Item::firstOrCreate(
                        ['facility_id' => $request->facility_id_selected, 'kode_material' => $itemFrom->kode_material],
                        ['nama_material' => $itemFrom->nama_material, 'stok_awal' => 0]
                    );

                    ItemTransaction::create([
                        'item_id' => $itemFrom->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'transfer',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAkhirAsal,
                        'region_from' => $itemFrom->region_id,
                        'facility_to' => $request->facility_id_selected,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => Carbon::parse($request->tanggal_transaksi),
                        'updated_at' => Carbon::parse($request->tanggal_transaksi),
                    ]);

                    return ['success' => true, 'message' => 'Transfer material berhasil dicatat!'];
                }

                if ($jenis_transaksi == 'penerimaan') {
                    // Cek stok di fasilitas asal
                    $itemFrom = Item::where('facility_id', $request->facility_id_selected)
                        ->where('kode_material', $request->kode_material)
                        ->first();

                    if (!$itemFrom || $itemFrom->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => 'Stok di SPBE/BPT asal tidak ada atau tidak mencukupi!']);
                    }

                    // Cari item spesifik yang menjadi tujuan di Pusat
                    $itemToPusat = Item::findOrFail($request->item_id_pusat);

                    $stokAwalAsal = $itemFrom->stok_akhir;
                    $stokAkhirAsal = $stokAwalAsal - $jumlah;

                    // Buat transaksi dengan `item_id` yang menunjuk ke item tujuan (Pusat).
                    ItemTransaction::create([
                        'item_id' => $itemToPusat->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'transfer',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwalAsal,
                        'stok_akhir_asal' => $stokAkhirAsal,
                        'facility_from' => $request->facility_id_selected,
                        'region_to' => $itemToPusat->region_id,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => Carbon::parse($request->tanggal_transaksi),
                        'updated_at' => Carbon::parse($request->tanggal_transaksi),
                    ]);

                    return ['success' => true, 'message' => 'Transfer material berhasil dicatat!'];
                }
            });
            return response()->json($response);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}
