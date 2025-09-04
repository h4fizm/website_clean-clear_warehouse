<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Region;
use App\Models\Facility;
use Carbon\Carbon;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $pusatRegion = Region::where('name_region', 'P.Layang (Pusat)')->firstOrFail();
        $sumselRegion = Region::where('name_region', 'SA Sumsel')->firstOrFail();
        $spbeSumsel = Facility::where('kode_plant', 'SSL001')->firstOrFail();

        // mapping bulan khusus untuk tiap material
        $materials = [
            'LPG 3 Kg' => [
                'date' => Carbon::create(2025, 7, 15, 10, 0, 0), // Juli
                'items' => [
                    ['condition' => 'Baru', 'code' => 'LPG3K-BARU', 'stock' => 150, 'location' => 'pusat'],
                    ['condition' => 'Baik', 'code' => 'LPG3K-BAIK', 'stock' => 200, 'location' => 'pusat'],
                    ['condition' => 'Rusak', 'code' => 'LPG3K-RUSAK', 'stock' => 50, 'location' => 'fasilitas'],
                    ['condition' => 'Afkir', 'code' => 'LPG3K-AFKIR', 'stock' => 25, 'location' => 'fasilitas'],
                ]
            ],
            'LPG 12 Kg' => [
                'date' => Carbon::create(2025, 8, 15, 10, 0, 0), // Agustus
                'items' => [
                    ['condition' => 'Baru', 'code' => 'LPG12K-BARU', 'stock' => 300, 'location' => 'pusat'],
                    ['condition' => 'Baik', 'code' => 'LPG12K-BAIK', 'stock' => 450, 'location' => 'fasilitas'],
                    ['condition' => 'Rusak', 'code' => 'LPG12K-RUSAK', 'stock' => 120, 'location' => 'pusat'],
                    ['condition' => 'Afkir', 'code' => 'LPG12K-AFKIR', 'stock' => 80, 'location' => 'fasilitas'],
                ]
            ],
            'Bright Gas 5.5 Kg' => [
                'date' => Carbon::create(2025, 6, 15, 10, 0, 0), // Juni
                'items' => [
                    ['condition' => 'Baru', 'code' => 'BG55K-BARU', 'stock' => 500, 'location' => 'pusat'],
                    ['condition' => 'Baik', 'code' => 'BG55K-BAIK', 'stock' => 600, 'location' => 'pusat'],
                    ['condition' => 'Rusak', 'code' => 'BG55K-RUSAK', 'stock' => 75, 'location' => 'fasilitas'],
                    ['condition' => 'Afkir', 'code' => 'BG55K-AFKIR', 'stock' => 30, 'location' => 'fasilitas'],
                ]
            ],
        ];

        foreach ($materials as $baseName => $data) {
            foreach ($data['items'] as $item) {
                Item::create([
                    'region_id' => $item['location'] === 'pusat' ? $pusatRegion->id : $sumselRegion->id,
                    'facility_id' => $item['location'] === 'fasilitas' ? $spbeSumsel->id : null,
                    'nama_material' => $baseName . ' - ' . $item['condition'],
                    'kode_material' => $item['code'],
                    'stok_awal' => $item['stock'],
                    'created_at' => $data['date'],
                    'updated_at' => $data['date'],
                ]);
            }
        }
    }
}
