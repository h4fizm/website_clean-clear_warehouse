<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Exception;

class TransactionController extends Controller
{
    /**
     * Menampilkan halaman utama transaksi dengan data fasilitas.
     */
    public function index(Request $request)
    {
        $regions = Region::where('nama_regions', '!=', 'Pusat (P.Layang)')->get();
        $selectedSalesAreaName = $request->query('sales_area', 'SA Jambi');
        $searchQuery = $request->query('search');
        $selectedRegion = Region::where('nama_regions', $selectedSalesAreaName)->first();
        $facilitiesQuery = $selectedRegion ? Plant::where('region_id', $selectedRegion->region_id) : Plant::query()->whereNull('region_id');

        if ($searchQuery) {
            $facilitiesQuery->where(function ($query) use ($searchQuery) {
                $query->where('nama_plant', 'like', '%' . $searchQuery . '%')
                    ->orWhere('kode_plant', 'like', '%' . $searchQuery . '%');
            });
        }

        $facilities = $facilitiesQuery->paginate(5);

        return view('dashboard_page.menu.data_transaksi', [
            'regions' => $regions,
            'facilities' => $facilities,
            'selectedSalesArea' => $selectedSalesAreaName,
            'search' => $searchQuery
        ]);
    }

    /**
     * Menampilkan form untuk membuat data facility baru.
     */
    public function create()
    {
        $regions = Region::where('nama_regions', '!=', 'Pusat (P.Layang)')->get();
        return view('dashboard_page.menu.tambah_spbe-bpt', ['regions' => $regions]);
    }

    /**
     * Menyimpan data facility baru ke database.
     */
    public function store(Request $request)
    {
        // Aturan validasi
        $rules = [
            'nama_plant' => 'required|string|max:255|unique:plants,nama_plant',
            'kode_plant' => 'required|string|max:255|unique:plants,kode_plant',
            'kategori_plant' => 'required|string|in:SPBE,BPT',
            'name_region' => 'required|string|exists:regions,nama_regions',
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
        ];

        // Pesan error kustom
        $messages = [
            'nama_plant.required' => 'Nama SPBE/BPT tidak boleh kosong.',
            'nama_plant.unique' => 'Nama SPBE/BPT ini sudah terdaftar.',
            'kode_plant.required' => 'Kode Plant tidak boleh kosong.',
            'kode_plant.unique' => 'Kode Plant ini sudah terdaftar.',
            'kategori_plant.required' => 'Jenis SPBE/BPT harus dipilih.',
            'name_region.required' => 'SA Region harus dipilih.',
            'provinsi.required' => 'Nama Provinsi tidak boleh kosong.',
            'kabupaten.required' => 'Nama Kabupaten tidak boleh kosong.',
        ];

        // Validasi input
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $region = Region::where('nama_regions', $request->name_region)->first();

            Plant::create([
                'nama_plant' => $request->nama_plant,
                'kode_plant' => $request->kode_plant,
                'kategori_plant' => $request->kategori_plant,
                'provinsi' => $request->provinsi,
                'kabupaten' => $request->kabupaten,
                'region_id' => $region->region_id,
            ]);

            return redirect()->route('transaksi.index')->with('success', 'Data SPBE/BPT baru berhasil ditambahkan.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.')->withInput();
        }
    }

    /**
     * Memperbarui data facility di database.
     */
    public function update(Request $request, Plant $plant)
    {
        // Aturan validasi
        $rules = [
            'nama_plant' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plants')->ignore($plant->plant_id)
            ],
            'kode_plant' => [
                'required',
                'string',
                'max:255',
                Rule::unique('plants')->ignore($plant->plant_id)
            ],
            'provinsi' => 'required|string|max:255',
            'kabupaten' => 'required|string|max:255',
        ];

        // Pesan error kustom
        $messages = [
            'nama_plant.required' => 'Nama SPBE/BPT tidak boleh kosong.',
            'nama_plant.unique' => 'Nama SPBE/BPT ini sudah digunakan oleh data lain.',
            'kode_plant.required' => 'Kode Plant tidak boleh kosong.',
            'kode_plant.unique' => 'Kode Plant ini sudah digunakan oleh data lain.',
            'provinsi.required' => 'Nama Provinsi tidak boleh kosong.',
            'kabupaten.required' => 'Nama Kabupaten tidak boleh kosong.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_plant_id', $plant->plant_id);
        }

        try {
            $plant->update($validator->validated());

            // Debug: Log request detection
            \Log::info('Update request detection', [
                'expectsJson' => $request->expectsJson(),
                'ajax' => $request->ajax(),
                'xRequestedWith' => $request->header('X-Requested-With'),
                'accept' => $request->header('Accept'),
                'contentType' => $request->header('Content-Type')
            ]);

            // Check if request is AJAX (multiple ways to detect)
            $isAjax = $request->expectsJson() ||
                     $request->ajax() ||
                     $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                     $request->header('Accept') === 'application/json';

            if ($isAjax) {
                \Log::info('Returning JSON response for AJAX request');
                return response()->json([
                    'success' => true,
                    'message' => 'Data SPBE/BPT berhasil diperbarui.'
                ]);
            }

            \Log::info('Returning redirect response for non-AJAX request');
            return redirect()->route('transaksi.index')->with('success', 'Data SPBE/BPT berhasil diperbarui.');
        } catch (\Exception $e) {
            // Check if request is AJAX
            $isAjax = $request->expectsJson() ||
                     $request->ajax() ||
                     $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                     $request->header('Accept') === 'application/json';

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem saat memperbarui data.'
                ], 500);
            }

            return redirect()->route('transaksi.index')->with('error', 'Terjadi kesalahan sistem saat memperbarui data.');
        }
    }

    /**
     * Menghapus data facility dari database.
     */
    public function destroy(Request $request, Plant $plant)
    {
        try {
            $plant->delete();

            // Check if request is AJAX (multiple ways to detect)
            $isAjax = $request->expectsJson() ||
                     $request->ajax() ||
                     $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                     $request->header('Accept') === 'application/json';

            if ($isAjax) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data SPBE/BPT berhasil dihapus.'
                ]);
            }

            return redirect()->route('transaksi.index')->with('success', 'Data SPBE/BPT berhasil dihapus.');
        } catch (Exception $e) {
            // Check if request is AJAX
            $isAjax = $request->expectsJson() ||
                     $request->ajax() ||
                     $request->header('X-Requested-With') === 'XMLHttpRequest' ||
                     $request->header('Accept') === 'application/json';

            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan saat menghapus data.'
                ], 500);
            }

            return redirect()->route('transaksi.index')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

    /**
     * API endpoint untuk DataTables - Get facilities data dengan server-side processing
     */
    public function getTransaksiFacilities(Request $request)
    {
        try {
            // Get DataTables parameters
            $draw = $request->get('draw', 1);
            $start = $request->get('start', 0);
            $length = $request->get('length', 10);
            $search = $request->get('search', []);
            $searchValue = $search['value'] ?? '';
            $order = $request->get('order', []);
            $columns = $request->get('columns', []);

            // Get sales area filter
            $salesArea = $request->get('sales_area', 'SA Jambi');

            // Base query
            $query = Plant::with('region')
                ->whereHas('region', function($q) use ($salesArea) {
                    $q->where('nama_regions', $salesArea);
                });

            // Search functionality
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('nama_plant', 'like', '%' . $searchValue . '%')
                      ->orWhere('kode_plant', 'like', '%' . $searchValue . '%')
                      ->orWhere('provinsi', 'like', '%' . $searchValue . '%')
                      ->orWhere('kabupaten', 'like', '%' . $searchValue . '%');
                });
            }

            // Get total records
            $totalRecords = $query->count();

            // Order functionality
            if (!empty($order)) {
                $orderColumn = $order[0]['column'];
                $orderDirection = $order[0]['dir'];

                // Map column index to database column
                $columnMap = [
                    0 => 'plant_id',
                    1 => 'nama_plant',
                    2 => 'kode_plant',
                    3 => 'provinsi',
                    4 => 'kabupaten',
                    5 => 'plant_id'
                ];

                if (isset($columnMap[$orderColumn])) {
                    $query->orderBy($columnMap[$orderColumn], $orderDirection);
                }
            } else {
                $query->orderBy('nama_plant', 'asc');
            }

            // Pagination
            $facilities = $query->offset($start)
                               ->limit($length)
                               ->get();

            // Format data for DataTables
            $data = $facilities->map(function($plant) {
                $actions = '';

                // Edit button
                $actions .= '<button type="button" class="btn btn-sm btn-info text-white me-1 edit-btn"
                                   data-plant-id="' . $plant->plant_id . '"
                                   data-plant-name="' . htmlspecialchars($plant->nama_plant) . '"
                                   data-plant-code="' . htmlspecialchars($plant->kode_plant) . '"
                                   data-plant-province="' . htmlspecialchars($plant->provinsi) . '"
                                   data-plant-regency="' . htmlspecialchars($plant->kabupaten) . '"
                                   onclick="openEditModal(' . $plant->plant_id . ')">
                                   <i class="fas fa-edit"></i>
                             </button>';

                // Delete form
                $actions .= '<form action="' . route('transaksi.destroy', $plant->plant_id) . '" method="POST" class="d-inline delete-form">
                               ' . csrf_field() . '
                               ' . method_field('DELETE') . '
                               <button type="submit" class="btn btn-sm btn-danger text-white delete-btn">
                                   <i class="fas fa-trash-alt"></i>
                               </button>
                           </form>';

                return [
                    'id' => $plant->plant_id,
                    'name' => $plant->nama_plant,
                    'kode_plant' => $plant->kode_plant,
                    'province' => $plant->provinsi,
                    'regency' => $plant->kabupaten,
                    'actions' => $actions,
                    'material_url' => route('materials.index', $plant->plant_id)
                ];
            });

            // Return DataTables response
            return response()->json([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $totalRecords,
                'data' => $data
            ]);

        } catch (Exception $e) {
            \Log::error('Error in getTransaksiFacilities: ' . $e->getMessage());

            return response()->json([
                'draw' => $request->get('draw', 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Internal server error'
            ], 500);
        }
    }
}