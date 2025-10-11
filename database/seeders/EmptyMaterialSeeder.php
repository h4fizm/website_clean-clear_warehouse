<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EmptyMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat struktur dasar regions dan plants tanpa data material sama sekali
     */
    public function run(): void
    {
        // Seed regions (diperlukan untuk struktur, tapi tidak ada material)
        $regions = [
            [
                'region_id' => 1,
                'nama_regions' => 'Pusat (P.Layang)',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'region_id' => 2,
                'nama_regions' => 'SA Jambi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'region_id' => 3,
                'nama_regions' => 'SA Bengkulu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'region_id' => 4,
                'nama_regions' => 'SA Lampung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'region_id' => 5,
                'nama_regions' => 'SA Bangsel',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'region_id' => 6,
                'nama_regions' => 'SA Sumsel',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('regions')->insert($regions);

        // Seed plants (diperlukan untuk struktur, tapi tidak ada material)
        $plants = [
            [
                'plant_id' => 1,
                'region_id' => 2, // SA Jambi
                'nama_plant' => 'SPBE Jambi Utama',
                'kode_plant' => 'SPBE-JMB-001',
                'kategori_plant' => 'SPBE',
                'provinsi' => 'Jambi',
                'kabupaten' => 'Kota Jambi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plant_id' => 2,
                'region_id' => 2, // SA Jambi
                'nama_plant' => 'BPT Jambi Selatan',
                'kode_plant' => 'BPT-JMB-001',
                'kategori_plant' => 'BPT',
                'provinsi' => 'Jambi',
                'kabupaten' => 'Kota Jambi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plant_id' => 3,
                'region_id' => 3, // SA Bengkulu
                'nama_plant' => 'SPBE Bengkulu Tengah',
                'kode_plant' => 'SPBE-BKL-001',
                'kategori_plant' => 'SPBE',
                'provinsi' => 'Bengkulu',
                'kabupaten' => 'Kota Bengkulu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plant_id' => 4,
                'region_id' => 3, // SA Bengkulu
                'nama_plant' => 'BPT Bengkulu Barat',
                'kode_plant' => 'BPT-BKL-001',
                'kategori_plant' => 'BPT',
                'provinsi' => 'Bengkulu',
                'kabupaten' => 'Kota Bengkulu',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plant_id' => 5,
                'region_id' => 4, // SA Lampung
                'nama_plant' => 'SPBE Lampung Selatan',
                'kode_plant' => 'SPBE-LPG-001',
                'kategori_plant' => 'SPBE',
                'provinsi' => 'Lampung',
                'kabupaten' => 'Lampung Selatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plant_id' => 6,
                'region_id' => 4, // SA Lampung
                'nama_plant' => 'BPT Bandar Lampung',
                'kode_plant' => 'BPT-BDL-001',
                'kategori_plant' => 'BPT',
                'provinsi' => 'Lampung',
                'kabupaten' => 'Kota Bandar Lampung',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plant_id' => 7,
                'region_id' => 5, // SA Bangsel
                'nama_plant' => 'SPBE Bangka Selatan',
                'kode_plant' => 'SPBE-BGS-001',
                'kategori_plant' => 'SPBE',
                'provinsi' => 'Kepulauan Bangka Belitung',
                'kabupaten' => 'Bangka Selatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'plant_id' => 8,
                'region_id' => 6, // SA Sumsel
                'nama_plant' => 'SPBE Palembang Barat',
                'kode_plant' => 'SPBE-PLB-001',
                'kategori_plant' => 'SPBE',
                'provinsi' => 'Sumatera Selatan',
                'kabupaten' => 'Kota Palembang',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('plants')->insert($plants);

        // Items table dikosongkan - tidak ada item sama sekali
        // User akan input item melalui CRUD system

        // Destination sales tetap dibuat untuk pilihan transaksi
        $destinations = [
            [
                'destination_id' => 1,
                'nama_tujuan' => 'Vendor UPP',
                'keterangan' => 'Tujuan pengiriman ke vendor UPP',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'destination_id' => 2,
                'nama_tujuan' => 'Sales Agen',
                'keterangan' => 'Pengiriman ke agen resmi',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'destination_id' => 3,
                'nama_tujuan' => 'Sales SPBE',
                'keterangan' => 'Distribusi ke SPBE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'destination_id' => 4,
                'nama_tujuan' => 'Sales BPT',
                'keterangan' => 'Distribusi ke BPT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('destination_sales')->insert($destinations);

        // Tabel-tabel material lainnya dikosongkan total:
        // - items: kosong (user input via CRUD)
        // - initial_stocks: kosong (user input via CRUD)
        // - current_stocks: kosong (user input via CRUD)
        // - transaction_logs: kosong (tercipta otomatis saat transaksi)
        // - destruction_submissions: kosong (user input via CRUD)

        $this->command->info('✅ Empty material structure created successfully!');
        $this->command->info('📝 All material tables are empty - ready for CRUD testing!');
    }
}