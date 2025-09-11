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
    public function run(): void
    {
        // Pastikan region 'Pusat' ada
        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();

        // Data material yang akan di-seed, semuanya di lokasi pusat
        $itemsToSeed = [
            // Kategori 'Baru' (2 items)
            [
                'nama_material' => 'Gas LPG 3 Kg',
                'kategori_material' => 'Baru',
                'kode_material' => 'LPG3K-BARU',
                'stok_awal' => 150,
                'date' => Carbon::create(2025, 8, 1, 10, 0, 0),
            ],
            [
                'nama_material' => 'Bright Gas 5.5 Kg',
                'kategori_material' => 'Baru',
                'kode_material' => 'BG55K-BARU',
                'stok_awal' => 500,
                'date' => Carbon::create(2025, 8, 5, 11, 0, 0),
            ],

            // Kategori 'Baik' (2 items)
            [
                'nama_material' => 'LPG 12 Kg',
                'kategori_material' => 'Baik',
                'kode_material' => 'LPG12K-BAIK',
                'stok_awal' => 450,
                'date' => Carbon::create(2025, 8, 2, 10, 0, 0),
            ],
            [
                'nama_material' => 'Elpiji Industri 50 Kg',
                'kategori_material' => 'Baik',
                'kode_material' => 'ELI50K-BAIK',
                'stok_awal' => 100,
                'date' => Carbon::create(2025, 8, 6, 11, 0, 0),
            ],

            // Kategori 'Rusak' (2 items)
            [
                'nama_material' => 'Solar Industri',
                'kategori_material' => 'Rusak',
                'kode_material' => 'SIL-RUSAK',
                'stok_awal' => 1500,
                'date' => Carbon::create(2025, 8, 3, 10, 0, 0),
            ],
            [
                'nama_material' => 'Oli Mesin',
                'kategori_material' => 'Rusak',
                'kode_material' => 'OLM-RUSAK',
                'stok_awal' => 300,
                'date' => Carbon::create(2025, 8, 7, 11, 0, 0),
            ],

            // Kategori 'Afkir' (5 items)
            [
                'nama_material' => 'Avtur',
                'kategori_material' => 'Afkir',
                'kode_material' => 'AVTR-AFKIR',
                'stok_awal' => 25,
                'date' => Carbon::create(2025, 8, 4, 10, 0, 0),
            ],
            [
                'nama_material' => 'Pertalite',
                'kategori_material' => 'Afkir',
                'kode_material' => 'PRTL-AFKIR',
                'stok_awal' => 80,
                'date' => Carbon::create(2025, 8, 8, 11, 0, 0),
            ],
            [
                'nama_material' => 'Pelumas Fastron',
                'kategori_material' => 'Afkir',
                'kode_material' => 'PFAS-AFKIR',
                'stok_awal' => 90,
                'date' => Carbon::create(2025, 8, 9, 12, 0, 0),
            ],
            [
                'nama_material' => 'Aspal Curah',
                'kategori_material' => 'Afkir',
                'kode_material' => 'ASPC-AFKIR',
                'stok_awal' => 110,
                'date' => Carbon::create(2025, 8, 10, 13, 0, 0),
            ],
            [
                'nama_material' => 'Minyak Tanah',
                'kategori_material' => 'Afkir',
                'kode_material' => 'MINT-AFKIR',
                'stok_awal' => 140,
                'date' => Carbon::create(2025, 8, 11, 14, 0, 0),
            ],
        ];

        // Looping untuk membuat data material
        foreach ($itemsToSeed as $item) {
            Item::create([
                'region_id' => $pusatRegion->id,
                'facility_id' => null,
                'nama_material' => $item['nama_material'], // Hapus penambahan kategori di nama material
                'kode_material' => $item['kode_material'],
                'kategori_material' => $item['kategori_material'],
                'stok_awal' => $item['stok_awal'],
                // Perbaikan: Tambahkan stok_akhir
                'stok_akhir' => $item['stok_awal'],
                'created_at' => $item['date'],
                'updated_at' => $item['date'],
            ]);
        }
    }
}