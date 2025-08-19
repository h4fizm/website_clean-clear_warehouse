<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            // SA Jambi
            ['name' => 'SPBE Jambi 1', 'kode_plant' => 'JMB001', 'province' => 'Jambi', 'regency' => 'Kota Jambi', 'type' => 'SPBE', 'region_id' => 2],
            ['name' => 'BPT Jambi 1', 'kode_plant' => 'JMB002', 'province' => 'Jambi', 'regency' => 'Muaro Jambi', 'type' => 'BPT', 'region_id' => 2],
            ['name' => 'SPBE Jambi 2', 'kode_plant' => 'JMB003', 'province' => 'Jambi', 'regency' => 'Batanghari', 'type' => 'SPBE', 'region_id' => 2],
            ['name' => 'BPT Jambi 2', 'kode_plant' => 'JMB004', 'province' => 'Jambi', 'regency' => 'Tanjung Jabung Barat', 'type' => 'BPT', 'region_id' => 2],
            ['name' => 'SPBE Jambi 3', 'kode_plant' => 'JMB005', 'province' => 'Jambi', 'regency' => 'Tanjung Jabung Timur', 'type' => 'SPBE', 'region_id' => 2],
            ['name' => 'BPT Jambi 3', 'kode_plant' => 'JMB006', 'province' => 'Jambi', 'regency' => 'Bungo', 'type' => 'BPT', 'region_id' => 2],
            // SA Bengkulu
            ['name' => 'SPBE Bengkulu 1', 'kode_plant' => 'BGL001', 'province' => 'Bengkulu', 'regency' => 'Kota Bengkulu', 'type' => 'SPBE', 'region_id' => 3],
            ['name' => 'BPT Bengkulu 1', 'kode_plant' => 'BGL002', 'province' => 'Bengkulu', 'regency' => 'Bengkulu Tengah', 'type' => 'BPT', 'region_id' => 3],
            ['name' => 'SPBE Bengkulu 2', 'kode_plant' => 'BGL003', 'province' => 'Bengkulu', 'regency' => 'Bengkulu Utara', 'type' => 'SPBE', 'region_id' => 3],
            ['name' => 'BPT Bengkulu 2', 'kode_plant' => 'BGL004', 'province' => 'Bengkulu', 'regency' => 'Seluma', 'type' => 'BPT', 'region_id' => 3],
            ['name' => 'SPBE Bengkulu 3', 'kode_plant' => 'BGL005', 'province' => 'Bengkulu', 'regency' => 'Kaur', 'type' => 'SPBE', 'region_id' => 3],
            ['name' => 'BPT Bengkulu 3', 'kode_plant' => 'BGL006', 'province' => 'Bengkulu', 'regency' => 'Rejang Lebong', 'type' => 'BPT', 'region_id' => 3],
            // SA Lampung
            ['name' => 'SPBE Lampung 1', 'kode_plant' => 'LMP001', 'province' => 'Lampung', 'regency' => 'Kota Bandar Lampung', 'type' => 'SPBE', 'region_id' => 4],
            ['name' => 'BPT Lampung 1', 'kode_plant' => 'LMP002', 'province' => 'Lampung', 'regency' => 'Lampung Selatan', 'type' => 'BPT', 'region_id' => 4],
            ['name' => 'SPBE Lampung 2', 'kode_plant' => 'LMP003', 'province' => 'Lampung', 'regency' => 'Lampung Tengah', 'type' => 'SPBE', 'region_id' => 4],
            ['name' => 'BPT Lampung 2', 'kode_plant' => 'LMP004', 'province' => 'Lampung', 'regency' => 'Lampung Timur', 'type' => 'BPT', 'region_id' => 4],
            ['name' => 'SPBE Lampung 3', 'kode_plant' => 'LMP005', 'province' => 'Lampung', 'regency' => 'Lampung Utara', 'type' => 'SPBE', 'region_id' => 4],
            ['name' => 'BPT Lampung 3', 'kode_plant' => 'LMP006', 'province' => 'Lampung', 'regency' => 'Mesuji', 'type' => 'BPT', 'region_id' => 4],
            // SA Sumsel
            ['name' => 'SPBE Sumsel 1', 'kode_plant' => 'SSL001', 'province' => 'Sumatera Selatan', 'regency' => 'Kota Palembang', 'type' => 'SPBE', 'region_id' => 5],
            ['name' => 'BPT Sumsel 1', 'kode_plant' => 'SSL002', 'province' => 'Sumatera Selatan', 'regency' => 'Banyuasin', 'type' => 'BPT', 'region_id' => 5],
            ['name' => 'SPBE Sumsel 2', 'kode_plant' => 'SSL003', 'province' => 'Sumatera Selatan', 'regency' => 'Ogan Komering Ilir', 'type' => 'SPBE', 'region_id' => 5],
            ['name' => 'BPT Sumsel 2', 'kode_plant' => 'SSL004', 'province' => 'Sumatera Selatan', 'regency' => 'Ogan Ilir', 'type' => 'BPT', 'region_id' => 5],
            ['name' => 'SPBE Sumsel 3', 'kode_plant' => 'SSL005', 'province' => 'Sumatera Selatan', 'regency' => 'Muara Enim', 'type' => 'SPBE', 'region_id' => 5],
            ['name' => 'BPT Sumsel 3', 'kode_plant' => 'SSL006', 'province' => 'Sumatera Selatan', 'regency' => 'Prabumulih', 'type' => 'BPT', 'region_id' => 5],
            // SA Babel
            ['name' => 'SPBE Babel 1', 'kode_plant' => 'BBL001', 'province' => 'Bangka Belitung', 'regency' => 'Kota Pangkal Pinang', 'type' => 'SPBE', 'region_id' => 6],
            ['name' => 'BPT Babel 1', 'kode_plant' => 'BBL002', 'province' => 'Bangka Belitung', 'regency' => 'Bangka', 'type' => 'BPT', 'region_id' => 6],
            ['name' => 'SPBE Babel 2', 'kode_plant' => 'BBL003', 'province' => 'Bangka Belitung', 'regency' => 'Bangka Barat', 'type' => 'SPBE', 'region_id' => 6],
            ['name' => 'BPT Babel 2', 'kode_plant' => 'BBL004', 'province' => 'Bangka Belitung', 'regency' => 'Bangka Selatan', 'type' => 'BPT', 'region_id' => 6],
            ['name' => 'SPBE Babel 3', 'kode_plant' => 'BBL005', 'province' => 'Bangka Belitung', 'regency' => 'Bangka Tengah', 'type' => 'SPBE', 'region_id' => 6],
            ['name' => 'BPT Babel 3', 'kode_plant' => 'BBL006', 'province' => 'Bangka Belitung', 'regency' => 'Belitung', 'type' => 'BPT', 'region_id' => 6],
        ];

        DB::table('facilities')->insert($facilities);
    }
}