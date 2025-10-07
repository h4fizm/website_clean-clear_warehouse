<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemTransaction;
use App\Models\Facility;
use App\Models\Region;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MaterialController extends Controller
{
    /**
     * Menampilkan daftar stok material untuk fasilitas tertentu.
     * Mencakup perbaikan filter dan query untuk menampilkan data yang lebih akurat.
     */
    public function index(Facility $facility, Request $request)
    {
        $filters = [
            'search' => $request->query('search'),
            'start_date' => $request->query('start_date'),
            'end_date' => $request->query('end_date'),
        ];

        // If no date filter is applied, default to the current month
        if (empty($filters['start_date']) && empty($filters['end_date'])) {
            $filters['start_date'] = \Carbon\Carbon::now()->startOfMonth()->toDateString();
            $filters['end_date'] = \Carbon\Carbon::now()->endOfMonth()->toDateString();
        }

        // ✅ PERBAIKAN: Tambahkan kondisi untuk hanya mengambil item yang aktif
        $itemsQuery = $facility->items()->select('items.*')
            ->where('is_active', true);

        $itemsQuery->when($filters['search'], function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('nama_material', 'like', '%' . $search . '%')
                    ->orWhere('kode_material', 'like', '%' . $search . '%');
            });
        });

        $itemsQuery->when($filters['start_date'] || $filters['end_date'], function ($query) use ($filters) {
            $query->where(function ($sub) use ($filters) {
                $sub->whereHas('transactions', function ($subQuery) use ($filters) {
                    if ($filters['start_date']) {
                        $subQuery->whereDate('created_at', '>=', $filters['start_date']);
                    }
                    if ($filters['end_date']) {
                        $subQuery->whereDate('created_at', '<=', $filters['end_date']);
                    }
                });
                if ($filters['start_date']) {
                    $sub->orWhereDate('items.updated_at', '>=', $filters['start_date']);
                }
                if ($filters['end_date']) {
                    $sub->whereDate('items.updated_at', '<=', $filters['end_date']);
                }
            });
        });

        $itemsQuery->addSelect([
            'penerimaan_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->where('jenis_transaksi', 'transfer')
                ->whereNotNull('facility_to')  // Ensure it's going TO a facility
                ->whereColumn('facility_to', 'items.facility_id')  // Only items going TO this facility
                ->whereExists(function ($subQuery) {
                    $subQuery->select(DB::raw(1))
                             ->from('items as source_item')
                             ->whereColumn('source_item.kode_material', 'items.kode_material')
                             ->whereColumn('source_item.id', 'base_transactions.item_id');
                })
                ->when($filters['start_date'], fn($query, $date) => $query->whereDate('created_at', '>=', $date))
                ->when($filters['end_date'], fn($query, $date) => $query->whereDate('created_at', '<=', $date)),

            'penyaluran_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->where('jenis_transaksi', 'transfer')
                ->whereNotNull('facility_from')  // Ensure it's coming FROM a facility
                ->whereColumn('facility_from', 'items.facility_id')  // Only items coming FROM this facility
                ->when($filters['start_date'], fn($query, $date) => $query->whereDate('created_at', '>=', $date))
                ->when($filters['end_date'], fn($query, $date) => $query->whereDate('created_at', '<=', $date)),

            'sales_total' => ItemTransaction::selectRaw('COALESCE(SUM(jumlah), 0)')
                ->whereColumn('item_id', 'items.id')
                ->where('jenis_transaksi', 'sales')
                ->when($filters['start_date'], fn($subQ, $date) => $subQ->whereDate('created_at', '>=', $date))
                ->when($filters['end_date'], fn($subQ, $date) => $subQ->whereDate('created_at', '<=', $date)),
        ]);

        $itemsQuery->orderByDesc(DB::raw("
            GREATEST(
                COALESCE(items.updated_at, '1970-01-01'),
                COALESCE((
                    SELECT MAX(it.created_at)
                    FROM base_transactions AS it
                    LEFT JOIN items AS source_item ON it.item_id = source_item.id
                    WHERE
                        it.item_id = items.id
                        OR
                        (it.facility_to = items.facility_id AND source_item.kode_material = items.kode_material)
                ), '1970-01-01')
            )
        "));

        $items = $itemsQuery->paginate(10)->withQueryString();
        $allFacilities = Facility::orderBy('name')->get(['id', 'name']);
        $locations = collect([['id' => 'pusat', 'name' => 'P.Layang (Pusat)']]);
        foreach ($allFacilities as $fac) {
            $locations->push(['id' => $fac->id, 'name' => $fac->name]);
        }

        return view('dashboard_page.list_material.data_material', [
            'facility' => $facility,
            'items' => $items,
            'filters' => $filters,
            'locations' => $locations,
            'pageTitle' => 'Daftar Stok Material - ' . $facility->name,
            'breadcrumbs' => ['Menu', 'Data Transaksi', 'Daftar Stok Material - ' . $facility->name],
        ]);
    }

    /**
     * Memperbarui data material.
     * Tidak ada perubahan logika besar di sini.
     */
    public function update(Request $request, Item $item)
    {
        $validator = Validator::make($request->all(), [
            'nama_material' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items')->where('facility_id', $item->facility_id)->ignore($item->id),
            ],
            'kode_material' => [
                'required',
                'string',
                'max:255',
                Rule::unique('items')->where('facility_id', $item->facility_id)->ignore($item->id),
            ],
            'stok_awal' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_item_id', $item->id);
        }

        DB::transaction(function () use ($request, $item) {
            $oldKode = $item->kode_material;
            $newKode = $request->input('kode_material');
            $newName = $request->input('nama_material');
            $stokAwalBaru = $request->stok_awal;

            // Only update stok_awal, keep stok_akhir as is
            $item->update([
                'nama_material' => $newName,
                'kode_material' => $newKode,
                'stok_awal' => $stokAwalBaru,
            ]);

            if ($oldKode !== $newKode) {
                Item::where('kode_material', $oldKode)
                    ->where('id', '!=', $item->id)
                    ->update([
                        'nama_material' => $newName,
                        'kode_material' => $newKode,
                    ]);
            } else {
                Item::where('kode_material', $oldKode)
                    ->where('id', '!=', $item->id)
                    ->update([
                        'nama_material' => $newName,
                    ]);
            }
        });

        return redirect()->route('materials.index', $item->facility_id)->with('success', 'Data material berhasil diperbarui!');
    }

    /**
     * Menghapus material secara permanen (hard delete) dari database.
     * Material dapat dihapus meskipun memiliki stok.
     */
    public function destroy(Item $item)
    {
        $facilityId = $item->facility_id;
        $materialName = $item->nama_material;

        try {
            DB::transaction(function () use ($item) {
                // Hapus semua transaksi terkait item ini di fasilitas
                $item->transactions()->delete();

                // Hapus item secara permanen dari database
                $item->delete();
            });
            return redirect()->route('materials.index', $facilityId)->with('success', "Material '{$materialName}' berhasil dihapus secara permanen.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', "Gagal menghapus material: " . $e->getMessage());
        }
    }

    /**
     * ✅ FUNGSI DIROMBAK TOTAL: Menangani semua jenis transaksi secara fleksibel.
     * Mencakup sales, transfer antar fasilitas, dan transfer ke/dari pusat.
     */
    /**
     * ✅ FUNGSI DIROMBAK TOTAL: Menangani semua jenis transaksi secara fleksibel.
     * Mencakup sales, transfer antar fasilitas, dan transfer ke/dari pusat.
     */
    public function processTransaction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|exists:items,id',
            'kode_material' => 'required|string',
            'jenis_transaksi' => 'required|in:penyaluran,penerimaan,sales',
            'jumlah' => 'required|integer|min:1',
            'tanggal_transaksi' => 'required|date',
            'no_surat_persetujuan' => 'nullable|string',
            'no_ba_serah_terima' => 'nullable|string',
            'asal_id' => 'required_if:jenis_transaksi,penyaluran,penerimaan|string',
            'tujuan_id' => 'required_if:jenis_transaksi,penyaluran,penerimaan|string|different:asal_id',
            'tujuan_sales' => 'required_if:jenis_transaksi,sales|string|in:Vendor UPP,Sales Agen,Sales BPT,Sales SPBE'
        ], [
            'tujuan_id.different' => 'Lokasi Tujuan tidak boleh sama dengan Lokasi Asal.'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $response = DB::transaction(function () use ($request) {
                $jenis = $request->jenis_transaksi;
                $jumlah = (int) $request->jumlah;
                $tanggal = Carbon::parse($request->tanggal_transaksi);
                $kodeMaterial = $request->kode_material;
                $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();

                if ($jenis === 'sales') {
                    $item = Item::where('id', $request->item_id)->lockForUpdate()->firstOrFail();
                    if ($item->stok_akhir < $jumlah) {
                        throw ValidationException::withMessages(['jumlah' => 'Stok tidak cukup!']);
                    }

                    $stokAwal = $item->stok_akhir;
                    $item->decrement('stok_akhir', $jumlah);

                    ItemTransaction::create([
                        'item_id' => $item->id,
                        'user_id' => Auth::id(),
                        'jenis_transaksi' => 'sales',
                        'jumlah' => $jumlah,
                        'stok_awal_asal' => $stokAwal,
                        'stok_akhir_asal' => $stokAwal - $jumlah,
                        'facility_from' => $item->facility_id,
                        'tujuan_sales' => $request->tujuan_sales,
                        'no_surat_persetujuan' => $request->no_surat_persetujuan,
                        'no_ba_serah_terima' => $request->no_ba_serah_terima,
                        'created_at' => $tanggal,
                        'updated_at' => $tanggal,
                    ]);

                    return ['success' => true, 'message' => 'Sales berhasil!'];
                }

                // Penyaluran/penerimaan
                $asalIsPusat = $request->asal_id === 'pusat';
                $tujuanIsPusat = $request->tujuan_id === 'pusat';

                $itemAsal = $asalIsPusat
                    ? Item::whereNull('facility_id')->where('kode_material', $kodeMaterial)->lockForUpdate()->firstOrFail()
                    : Item::where('facility_id', $request->asal_id)->where('kode_material', $kodeMaterial)->lockForUpdate()->firstOrFail();

                if ($itemAsal->stok_akhir < $jumlah) {
                    $lokasi = $asalIsPusat ? 'Gudang Pusat' : $itemAsal->facility->name;
                    throw ValidationException::withMessages(['jumlah' => "Stok di {$lokasi} tidak mencukupi!"]);
                }

                // PERBAIKAN: Cari item tujuan termasuk yang tidak aktif
                $itemTujuan = $tujuanIsPusat
                    ? Item::whereNull('facility_id')->where('kode_material', $kodeMaterial)->lockForUpdate()->firstOrFail()
                    : Item::where('facility_id', $request->tujuan_id)->where('kode_material', $kodeMaterial)->lockForUpdate()->first();

                // Jika item tujuan tidak ditemukan, buat item baru
                if (!$itemTujuan) {
                    $itemTujuan = Item::create([
                        'facility_id' => $request->tujuan_id,
                        'kode_material' => $kodeMaterial,
                        'nama_material' => $itemAsal->nama_material,
                        'stok_awal' => 0,
                        'stok_akhir' => 0,
                        'kategori_material' => $itemAsal->kategori_material,
                        'region_id' => Facility::findOrFail($request->tujuan_id)->region_id,
                        'is_active' => true,
                    ]);
                }
                // Jika item tujuan ditemukan tetapi tidak aktif, aktifkan kembali
                else if (!$itemTujuan->is_active) {
                    $itemTujuan->update(['is_active' => true]);
                }
                // Pastikan item tujuan di-lock setelah dibuat.
                $itemTujuan->lockForUpdate();

                $stokAwalAsal = $itemAsal->stok_akhir;
                $stokAwalTujuan = $itemTujuan->stok_akhir;

                $itemAsal->decrement('stok_akhir', $jumlah);
                $itemTujuan->increment('stok_akhir', $jumlah);

                // Logika pencatatan transaksi yang lebih sederhana
                ItemTransaction::create([
                    'item_id' => $itemAsal->id,
                    'user_id' => Auth::id(),
                    'jenis_transaksi' => 'transfer',
                    'jumlah' => $jumlah,
                    'stok_awal_asal' => $stokAwalAsal,
                    'stok_akhir_asal' => $stokAwalAsal - $jumlah,
                    'stok_awal_tujuan' => $stokAwalTujuan,
                    'stok_akhir_tujuan' => $stokAwalTujuan + $jumlah,
                    'facility_from' => $asalIsPusat ? null : $itemAsal->facility_id,
                    'region_from' => $asalIsPusat ? $itemAsal->region_id : null,
                    'facility_to' => $tujuanIsPusat ? null : $itemTujuan->facility_id,
                    'region_to' => $tujuanIsPusat ? $itemTujuan->region_id : null,
                    'no_surat_persetujuan' => $request->no_surat_persetujuan,
                    'no_ba_serah_terima' => $request->no_ba_serah_terima,
                    'created_at' => $tanggal,
                    'updated_at' => $tanggal,
                ]);

                return ['success' => true, 'message' => 'Transfer berhasil!'];
            });

            return response()->json($response);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Facility Transfer Error: ' . $e->getMessage() . ' line: ' . $e->getLine());
            return response()->json(['message' => 'Terjadi kesalahan server.'], 500);
        }
    }

    /**
     * API endpoint for DataTables to fetch facility materials data
     */
    public function getFacilityMaterials(Request $request, Facility $facility)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'] ?? '';
        $order = $request->get('order')[0] ?? null;
        $columns = $request->get('columns') ?? [];

        // Build the query
        $query = Item::query()
            ->where('facility_id', $facility->id)
            ->where('is_active', true);

        // Add search functionality
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_material', 'like', "%{$search}%")
                  ->orWhere('kode_material', 'like', "%{$search}%");
            });
        }

        // Get total records count
        $totalRecords = $query->count();

        // Add ordering
        if ($order) {
            $columnIndex = $order['column'];
            $direction = $order['dir'];
            $columnName = $columns[$columnIndex]['data'] ?? 'id';

            switch ($columnName) {
                case 'kode_material':
                    $query->orderBy('kode_material', $direction);
                    break;
                case 'nama_material':
                    $query->orderBy('nama_material', $direction);
                    break;
                case 'stok_awal':
                    $query->orderBy('stok_awal', $direction);
                    break;
                case 'penerimaan_total':
                    $query->orderByRaw('COALESCE((SELECT SUM(jumlah) FROM base_transactions WHERE jenis_transaksi = "transfer" AND facility_to = items.facility_id AND EXISTS (SELECT 1 FROM items as source_item WHERE source_item.kode_material = items.kode_material AND source_item.id = base_transactions.item_id)), 0)', $direction);
                    break;
                case 'penyaluran_total':
                    $query->orderByRaw('COALESCE((SELECT SUM(jumlah) FROM base_transactions WHERE jenis_transaksi = "transfer" AND facility_from = items.facility_id), 0)', $direction);
                    break;
                case 'sales_total':
                    $query->orderByRaw('COALESCE((SELECT SUM(jumlah) FROM base_transactions WHERE item_id = items.id AND jenis_transaksi = "sales"), 0)', $direction);
                    break;
                case 'stok_akhir':
                    $query->orderBy('stok_akhir', $direction);
                    break;
                default:
                    $query->orderBy('updated_at', $direction);
                    break;
            }
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        // Add pagination
        $query->offset($start)->limit($length);

        // Get the items with calculated totals
        $items = $query->get();

        $data = [];
        foreach ($items as $item) {
            // Calculate totals for each item
            $penerimaanTotal = ItemTransaction::where('jenis_transaksi', 'transfer')
                ->whereNotNull('facility_to')
                ->where('facility_to', $item->facility_id)
                ->whereExists(function ($subQuery) use ($item) {
                    $subQuery->select(DB::raw(1))
                             ->from('items as source_item')
                             ->whereColumn('source_item.kode_material', DB::raw("'" . $item->kode_material . "'"))
                             ->whereColumn('source_item.id', 'base_transactions.item_id');
                })
                ->sum('jumlah');

            $penyaluranTotal = ItemTransaction::where('jenis_transaksi', 'transfer')
                ->whereNotNull('facility_from')
                ->where('facility_from', $item->facility_id)
                ->sum('jumlah');

            $salesTotal = ItemTransaction::where('item_id', $item->id)
                ->where('jenis_transaksi', 'sales')
                ->sum('jumlah');

            // Calculate the final stock
            $stokAkhir = $item->stok_awal + $penerimaanTotal - $penyaluranTotal - $salesTotal;

            $data[] = [
                'id' => $item->id,
                'kode_material' => $item->kode_material,
                'nama_material' => $item->nama_material,
                'kategori_material' => $item->kategori_material,
                'stok_awal' => $item->stok_awal,
                'penerimaan_total' => $penerimaanTotal,
                'penyaluran_total' => $penyaluranTotal,
                'sales_total' => $salesTotal,
                'pemusnahan_total' => 0, // SPBE/BPT doesn't track pemusnahan
                'stok_akhir' => $stokAkhir,
                'actions' => '<a href="' . route('materials.update', $item->id) . '" class="btn btn-sm btn-primary">Edit</a> '
                    . '<form action="' . route('materials.destroy', $item->id) . '" method="POST" class="d-inline">'
                    . csrf_field() . method_field('DELETE') 
                    . '<button type="submit" class="btn btn-sm btn-danger" onclick="return confirm(\'Yakin ingin menghapus?\')">Hapus</button>'
                    . '</form>'
            ];
        }

        return response()->json([
            'draw' => (int) $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords, // Note: This should be updated to reflect filtered count
            'data' => $data
        ]);
    }
}