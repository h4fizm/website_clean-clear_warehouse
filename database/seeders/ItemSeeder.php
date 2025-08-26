<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;
use App\Models\Region;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $pLayang = Region::where('name_region', 'P.Layang (Pusat)')->first();

        $items = [
            ['nama_material' => 'LPG 3 Kg - Baik', 'kode_material' => 'LPG3K-BAIK'],
            ['nama_material' => 'LPG 3 Kg - Rusak', 'kode_material' => 'LPG3K-RUSAK'],
            ['nama_material' => 'LPG 3 Kg - Retur', 'kode_material' => 'LPG3K-RETUR'],
            ['nama_material' => 'LPG 3 Kg - Musnah', 'kode_material' => 'LPG3K-MUSNAH'],
            ['nama_material' => 'Bright Gas 5.5 Kg', 'kode_material' => 'BG55K'],
            ['nama_material' => 'Bright Gas 12 Kg', 'kode_material' => 'BG12K'],
            ['nama_material' => 'LPG 12 Kg', 'kode_material' => 'LPG12K'],
            ['nama_material' => 'LPG 50 Kg', 'kode_material' => 'LPG50K'],
            ['nama_material' => 'LPG Bulk 100 Kg', 'kode_material' => 'LPGB100'],
            ['nama_material' => 'LPG Bulk 200 Kg', 'kode_material' => 'LPGB200'],
            ['nama_material' => 'LPG Bulk 500 Kg', 'kode_material' => 'LPGB500'],
        ];

        foreach ($items as $item) {
            Item::create([
                'region_id' => $pLayang->id,
                'facility_id' => null, // khusus P. Layang bukan facility
                'nama_material' => $item['nama_material'],
                'kode_material' => $item['kode_material'],
                'stok_awal' => 1000, // semua stok awal fix 1000
            ]);
        }
    }
}
