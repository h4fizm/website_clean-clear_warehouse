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
}