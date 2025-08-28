<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $facilities = [
            // Region 2 - Jambi
            ['name' => 'SPBE Jambi', 'kode_plant' => 'JMB001', 'province' => 'Jambi', 'regency' => 'Kota Jambi', 'type' => 'SPBE', 'region_id' => 2],
            ['name' => 'BPT Jambi', 'kode_plant' => 'JMB002', 'province' => 'Jambi', 'regency' => 'Muaro Jambi', 'type' => 'BPT', 'region_id' => 2],

            // Region 3 - Bengkulu
            ['name' => 'SPBE Bengkulu', 'kode_plant' => 'BGL001', 'province' => 'Bengkulu', 'regency' => 'Kota Bengkulu', 'type' => 'SPBE', 'region_id' => 3],
            ['name' => 'BPT Bengkulu', 'kode_plant' => 'BGL002', 'province' => 'Bengkulu', 'regency' => 'Bengkulu Tengah', 'type' => 'BPT', 'region_id' => 3],

            // Region 4 - Lampung
            ['name' => 'SPBE Lampung', 'kode_plant' => 'LMP001', 'province' => 'Lampung', 'regency' => 'Bandar Lampung', 'type' => 'SPBE', 'region_id' => 4],
            ['name' => 'BPT Lampung', 'kode_plant' => 'LMP002', 'province' => 'Lampung', 'regency' => 'Lampung Selatan', 'type' => 'BPT', 'region_id' => 4],

            // Region 5 - Sumsel
            ['name' => 'SPBE Sumsel', 'kode_plant' => 'SSL001', 'province' => 'Sumatera Selatan', 'regency' => 'Palembang', 'type' => 'SPBE', 'region_id' => 5],
            ['name' => 'BPT Sumsel', 'kode_plant' => 'SSL002', 'province' => 'Sumatera Selatan', 'regency' => 'Banyuasin', 'type' => 'BPT', 'region_id' => 5],

            // Region 6 - Bangka Belitung
            ['name' => 'SPBE Babel', 'kode_plant' => 'BBL001', 'province' => 'Bangka Belitung', 'regency' => 'Pangkal Pinang', 'type' => 'SPBE', 'region_id' => 6],
            ['name' => 'BPT Babel', 'kode_plant' => 'BBL002', 'province' => 'Bangka Belitung', 'regency' => 'Bangka', 'type' => 'BPT', 'region_id' => 6],
        ];

        DB::table('facilities')->insert($facilities);
    }
}
