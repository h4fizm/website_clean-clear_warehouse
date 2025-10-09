<?php

namespace App\Http\Controllers;

use App\Models\Facility;
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
        $regions = Region::where('name_region', '!=', 'P.Layang (Pusat)')->get();
        $selectedSalesAreaName = $request->query('sales_area', 'SA Jambi');
        $searchQuery = $request->query('search');
        $selectedRegion = Region::where('name_region', $selectedSalesAreaName)->first();
        $facilitiesQuery = $selectedRegion ? $selectedRegion->facilities() : Facility::query()->whereNull('id');

        if ($searchQuery) {
            $facilitiesQuery->where(function ($query) use ($searchQuery) {
                $query->where('name', 'like', '%' . $searchQuery . '%')
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
        $regions = Region::where('name_region', '!=', 'P.Layang (Pusat)')->get();
        return view('dashboard_page.menu.tambah_spbe-bpt', ['regions' => $regions]);
    }

    /**
     * Menyimpan data facility baru ke database.
     */
    public function store(Request $request)
    {
        // Aturan validasi
        $rules = [
            'name' => 'required|string|max:255|unique:facilities,name',
            'kode_plant' => 'required|string|max:255|unique:facilities,kode_plant',
            'type' => 'required|string|in:SPBE,BPT',
            'name_region' => 'required|string|exists:regions,name_region',
            'province' => 'required|string|max:255',
            'regency' => 'required|string|max:255',
        ];

        // Pesan error kustom
        $messages = [
            'name.required' => 'Nama SPBE/BPT tidak boleh kosong.',
            'name.unique' => 'Nama SPBE/BPT ini sudah terdaftar.',
            'kode_plant.required' => 'Kode Plant tidak boleh kosong.',
            'kode_plant.unique' => 'Kode Plant ini sudah terdaftar.',
            'type.required' => 'Jenis SPBE/BPT harus dipilih.',
            'name_region.required' => 'SA Region harus dipilih.',
            'province.required' => 'Nama Provinsi tidak boleh kosong.',
            'regency.required' => 'Nama Kabupaten tidak boleh kosong.',
        ];

        // Validasi input
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $region = Region::where('name_region', $request->name_region)->first();

            Facility::create([
                'name' => $request->name,
                'kode_plant' => $request->kode_plant,
                'type' => $request->type,
                'province' => $request->province,
                'regency' => $request->regency,
                'region_id' => $region->id,
            ]);

            return redirect()->route('transaksi.index')->with('success', 'Data SPBE/BPT baru berhasil ditambahkan.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat menyimpan data.')->withInput();
        }
    }

    /**
     * Memperbarui data facility di database.
     */
    public function update(Request $request, Facility $facility)
    {
        // Aturan validasi
        $rules = [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('facilities')->ignore($facility->id)
            ],
            'kode_plant' => [
                'required',
                'string',
                'max:255',
                Rule::unique('facilities')->ignore($facility->id)
            ],
            'province' => 'required|string|max:255',
            'regency' => 'required|string|max:255',
        ];

        // Pesan error kustom
        $messages = [
            'name.required' => 'Nama SPBE/BPT tidak boleh kosong.',
            'name.unique' => 'Nama SPBE/BPT ini sudah digunakan oleh data lain.',
            'kode_plant.required' => 'Kode Plant tidak boleh kosong.',
            'kode_plant.unique' => 'Kode Plant ini sudah digunakan oleh data lain.',
            'province.required' => 'Nama Provinsi tidak boleh kosong.',
            'regency.required' => 'Nama Kabupaten tidak boleh kosong.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error_facility_id', $facility->id);
        }

        try {
            $facility->update($validator->validated());
            return redirect()->back()->with('success', 'Data SPBE/BPT berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat memperbarui data.');
        }
    }

    /**
     * Menghapus data facility dari database.
     */
    public function destroy(Facility $facility)
    {
        try {
            $facility->delete();
            return redirect()->back()->with('success', 'Data SPBE/BPT berhasil dihapus.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat menghapus data.');
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
            $query = Facility::with('region')
                ->whereHas('region', function($q) use ($salesArea) {
                    $q->where('name_region', $salesArea);
                });

            // Search functionality
            if (!empty($searchValue)) {
                $query->where(function($q) use ($searchValue) {
                    $q->where('name', 'like', '%' . $searchValue . '%')
                      ->orWhere('kode_plant', 'like', '%' . $searchValue . '%')
                      ->orWhere('province', 'like', '%' . $searchValue . '%')
                      ->orWhere('regency', 'like', '%' . $searchValue . '%');
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
                    0 => 'id',
                    1 => 'name',
                    2 => 'kode_plant',
                    3 => 'province',
                    4 => 'regency',
                    5 => 'id'
                ];

                if (isset($columnMap[$orderColumn])) {
                    $query->orderBy($columnMap[$orderColumn], $orderDirection);
                }
            } else {
                $query->orderBy('name', 'asc');
            }

            // Pagination
            $facilities = $query->offset($start)
                               ->limit($length)
                               ->get();

            // Format data for DataTables
            $data = $facilities->map(function($facility) {
                $actions = '';

                // Edit button
                $actions .= '<button type="button" class="btn btn-sm btn-info text-white me-1 edit-btn"
                                   data-bs-toggle="modal"
                                   data-bs-target="#editSpbeBptModal-' . $facility->id . '">
                                   <i class="fas fa-edit"></i>
                             </button>';

                // Delete form
                $actions .= '<form action="' . route('transaksi.destroy', $facility->id) . '" method="POST" class="d-inline delete-form">
                               ' . csrf_field() . '
                               ' . method_field('DELETE') . '
                               <button type="submit" class="btn btn-sm btn-danger text-white delete-btn">
                                   <i class="fas fa-trash-alt"></i>
                               </button>
                           </form>';

                return [
                    'id' => $facility->id,
                    'name' => $facility->name,
                    'kode_plant' => $facility->kode_plant,
                    'province' => $facility->province,
                    'regency' => $facility->regency,
                    'actions' => $actions,
                    'material_url' => route('materials.index', $facility)
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