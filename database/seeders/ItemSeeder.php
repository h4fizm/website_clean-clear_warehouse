<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Region;
use App\Models\Facility;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Mengambil Region & Facility berdasarkan data PASTI dari seeder Anda
        // Digunakan firstOrFail() agar seeder gagal jika Region/Facility belum di-seed.
        // Ini memastikan urutan seeder benar.
        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();
        $sumselRegion = Region::where('name_region', 'SA Sumsel')->firstOrFail();
        $spbeSumsel = Facility::where('kode_plant', 'SSL001')->firstOrFail(); // Mengambil SPBE Sumsel

        // 2. DEFINISIKAN DATA STOK
        $materials = [
            'LPG 3 Kg' => [
                ['condition' => 'Baik', 'code' => 'LPG3K-BAIK', 'stock' => 200, 'location' => 'pusat'],
                ['condition' => 'Rusak', 'code' => 'LPG3K-RUSAK', 'stock' => 300, 'location' => 'pusat'],
                ['condition' => 'Retur', 'code' => 'LPG3K-RETUR', 'stock' => 400, 'location' => 'fasilitas'],
                ['condition' => 'Musnah', 'code' => 'LPG3K-MUSNAH', 'stock' => 100, 'location' => 'fasilitas'],
            ],
            'LPG 12 Kg' => [
                ['condition' => 'Baik', 'code' => 'LPG12K-BAIK', 'stock' => 500, 'location' => 'pusat'],
                ['condition' => 'Rusak', 'code' => 'LPG12K-RUSAK', 'stock' => 250, 'location' => 'fasilitas'],
                ['condition' => 'Retur', 'code' => 'LPG12K-RETUR', 'stock' => 150, 'location' => 'pusat'],
                ['condition' => 'Musnah', 'code' => 'LPG12K-MUSNAH', 'stock' => 100, 'location' => 'fasilitas'],
            ],
            'Bright Gas 5.5 Kg' => [
                ['condition' => 'Baik', 'code' => 'BG55K-BAIK', 'stock' => 700, 'location' => 'pusat'],
                ['condition' => 'Rusak', 'code' => 'BG55K-RUSAK', 'stock' => 100, 'location' => 'pusat'],
                ['condition' => 'Retur', 'code' => 'BG55K-RETUR', 'stock' => 100, 'location' => 'fasilitas'],
                ['condition' => 'Musnah', 'code' => 'BG55K-MUSNAH', 'stock' => 100, 'location' => 'fasilitas'],
            ],
        ];

        // 3. LOOPING DAN BUAT ITEM SESUAI LOKASI
        foreach ($materials as $baseName => $variations) {
            foreach ($variations as $item) {
                Item::create([
                    // Menggunakan ID dari data yang sudah pasti ada
                    'region_id' => $item['location'] === 'pusat' ? $pusatRegion->id : $sumselRegion->id,
                    'facility_id' => $item['location'] === 'fasilitas' ? $spbeSumsel->id : null,
                    'nama_material' => $baseName . ' - ' . $item['condition'],
                    'kode_material' => $item['code'],
                    'stok_awal' => $item['stock'],
                ]);
            }
        }
    }
}
