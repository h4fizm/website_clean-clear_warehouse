<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;
use App\Models\Region;

class FacilitySeeder extends Seeder
{
    public function run()
    {
        $regions = Region::all();
        
        $facilities = [
            // P.Layang (Pusat) - index 0
            ['name' => 'Pusat Bandung', 'kode_plant' => 'PB001', 'province' => 'Jawa Barat', 'regency' => 'Bandung', 'type' => 'BPT', 'region_id' => $regions[0]->id],
            ['name' => 'SPBE Sumedang', 'kode_plant' => 'SS002', 'province' => 'Jawa Barat', 'regency' => 'Sumedang', 'type' => 'SPBE', 'region_id' => $regions[0]->id],
            ['name' => 'SPBE Cimahi', 'kode_plant' => 'SC003', 'province' => 'Jawa Barat', 'regency' => 'Cimahi', 'type' => 'SPBE', 'region_id' => $regions[0]->id],
            
            // Jawa Barat - index 1
            ['name' => 'Pusat Bandung Regional', 'kode_plant' => 'PBR002', 'province' => 'Jawa Barat', 'regency' => 'Bandung', 'type' => 'BPT', 'region_id' => $regions[1]->id],
            ['name' => 'SPBE Sumedang Regional', 'kode_plant' => 'SSR003', 'province' => 'Jawa Barat', 'regency' => 'Sumedang', 'type' => 'SPBE', 'region_id' => $regions[1]->id],
            
            // Jawa Tengah - index 2
            ['name' => 'Pusat Semarang', 'kode_plant' => 'PS004', 'province' => 'Jawa Tengah', 'regency' => 'Semarang', 'type' => 'BPT', 'region_id' => $regions[2]->id],
            ['name' => 'SPBE Solo', 'kode_plant' => 'SO005', 'province' => 'Jawa Tengah', 'regency' => 'Solo', 'type' => 'SPBE', 'region_id' => $regions[2]->id],
            
            // Jawa Timur - index 3
            ['name' => 'Pusat Surabaya', 'kode_plant' => 'PSU006', 'province' => 'Jawa Timur', 'regency' => 'Surabaya', 'type' => 'BPT', 'region_id' => $regions[3]->id],
            ['name' => 'SPBE Malang', 'kode_plant' => 'SM007', 'province' => 'Jawa Timur', 'regency' => 'Malang', 'type' => 'SPBE', 'region_id' => $regions[3]->id],
            ['name' => 'SPBE Sidoarjo', 'kode_plant' => 'SD008', 'province' => 'Jawa Timur', 'regency' => 'Sidoarjo', 'type' => 'SPBE', 'region_id' => $regions[3]->id],
            
            // DKI Jakarta - index 4
            ['name' => 'Pusat Jakarta', 'kode_plant' => 'PJ009', 'province' => 'DKI Jakarta', 'regency' => 'Jakarta Pusat', 'type' => 'BPT', 'region_id' => $regions[4]->id],
            
            // Banten - index 5
            ['name' => 'SPBE Tangerang', 'kode_plant' => 'TGR010', 'province' => 'Banten', 'regency' => 'Tangerang', 'type' => 'SPBE', 'region_id' => $regions[5]->id],
            ['name' => 'SPBE Serang', 'kode_plant' => 'SER011', 'province' => 'Banten', 'regency' => 'Serang', 'type' => 'SPBE', 'region_id' => $regions[5]->id],
        ];

        foreach ($facilities as $facility) {
            Facility::create($facility);
        }
    }
}