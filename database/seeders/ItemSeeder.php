<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Region;
use App\Models\Facility;
use Carbon\Carbon;

class ItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Temukan region 'P.Layang (Pusat)'
        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();

        // Temukan facility dengan ID 1
        $facility = Facility::findOrFail(1);

        // Data material untuk Gudang Region (Pusat)
        $pusatItems = [
            // Kategori 'Baru'
            [
                'nama_material' => 'Gas LPG 3 Kg',
                'kategori_material' => 'Baru',
                'kode_material' => 'LPG3K-BARU',
            ],
            // Kategori 'Baik'
            [
                'nama_material' => 'LPG 12 Kg',
                'kategori_material' => 'Baik',
                'kode_material' => 'LPG12K-BAIK',
            ],
            // Kategori 'Rusak'
            [
                'nama_material' => 'Solar Industri',
                'kategori_material' => 'Rusak',
                'kode_material' => 'SIL-RUSAK',
            ],
            // Kategori 'Afkir'
            [
                'nama_material' => 'Avtur',
                'kategori_material' => 'Afkir',
                'kode_material' => 'AVTR-AFKIR',
            ],
            [
                'nama_material' => 'Pertalite',
                'kategori_material' => 'Afkir',
                'kode_material' => 'PRTL-AFKIR',
            ],
        ];

        // Data material untuk Fasilitas (SPBE/BPT)
        $facilityItems = [
            // Kategori 'Baru'
            [
                'nama_material' => 'Bright Gas 5.5 Kg',
                'kategori_material' => 'Baru',
                'kode_material' => 'BG55K-BARU',
            ],
            // Kategori 'Baik'
            [
                'nama_material' => 'Elpiji Industri 50 Kg',
                'kategori_material' => 'Baik',
                'kode_material' => 'ELI50K-BAIK',
            ],
            // Kategori 'Rusak'
            [
                'nama_material' => 'Oli Mesin',
                'kategori_material' => 'Rusak',
                'kode_material' => 'OLM-RUSAK',
            ],
            // Kategori 'Afkir'
            [
                'nama_material' => 'Pelumas Fastron',
                'kategori_material' => 'Afkir',
                'kode_material' => 'PFAS-AFKIR',
            ],
            [
                'nama_material' => 'Aspal Curah',
                'kategori_material' => 'Afkir',
                'kode_material' => 'ASPC-AFKIR',
            ],
        ];

        // Mengisi data untuk Gudang Region (Pusat)
        foreach ($pusatItems as $item) {
            Item::create([
                'region_id' => $pusatRegion->id,
                'facility_id' => null,
                'nama_material' => $item['nama_material'],
                'kode_material' => $item['kode_material'],
                'kategori_material' => $item['kategori_material'],
                'stok_awal' => 1000,
                'stok_akhir' => 1000,
            ]);
        }

        // Mengisi data untuk Fasilitas (ID 1)
        foreach ($facilityItems as $item) {
            Item::create([
                'region_id' => null,
                'facility_id' => $facility->id,
                'nama_material' => $item['nama_material'],
                'kode_material' => $item['kode_material'],
                'kategori_material' => $item['kategori_material'],
                'stok_awal' => 1000,
                'stok_akhir' => 1000,
            ]);
        }
    }
}
