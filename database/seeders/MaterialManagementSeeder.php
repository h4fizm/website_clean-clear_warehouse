<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class MaterialManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed regions
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

        // Seed plants
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

        // Seed items
        $items = [
            [
                'item_id' => 1,
                'nama_material' => 'Tabung LPG 3 Kg',
                'kode_material' => 'TBL-3KG',
                'kategori_material' => 'Baik',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => 2,
                'nama_material' => 'Tabung LPG 12 Kg',
                'kode_material' => 'TBL-12KG',
                'kategori_material' => 'Baru',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => 3,
                'nama_material' => 'Regulator LPG',
                'kode_material' => 'REG-LPG',
                'kategori_material' => 'Afkir',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('items')->insert($items);

        // Seed destination_sales
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

        // Seed initial_stocks
        $initialStocks = [
            [
                'initial_stock_id' => 1,
                'item_id' => 1,
                'quantity' => 1000.00,
                'tanggal_masuk' => now()->subDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'initial_stock_id' => 2,
                'item_id' => 2,
                'quantity' => 500.00,
                'tanggal_masuk' => now()->subDays(30),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('initial_stocks')->insert($initialStocks);

        // Seed current_stocks - hanya Pusat dan SPBE/BPT yang bisa menyimpan material
        $currentStocks = [
            [
                'stock_id' => 1,
                'lokasi_id' => 1, // Pusat (P.Layang) - bisa menyimpan material
                'item_id' => 1,
                'current_quantity' => 600.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock_id' => 2,
                'lokasi_id' => 1, // SPBE Jambi Utama
                'item_id' => 1,
                'current_quantity' => 200.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock_id' => 3,
                'lokasi_id' => 2, // BPT Jambi Selatan
                'item_id' => 2,
                'current_quantity' => 150.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock_id' => 4,
                'lokasi_id' => 3, // SPBE Bengkulu Tengah
                'item_id' => 1,
                'current_quantity' => 100.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock_id' => 5,
                'lokasi_id' => 4, // BPT Bengkulu Barat
                'item_id' => 2,
                'current_quantity' => 75.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock_id' => 6,
                'lokasi_id' => 5, // SPBE Lampung Selatan
                'item_id' => 1,
                'current_quantity' => 110.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock_id' => 7,
                'lokasi_id' => 6, // BPT Bandar Lampung
                'item_id' => 2,
                'current_quantity' => 80.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock_id' => 8,
                'lokasi_id' => 7, // SPBE Bangka Selatan
                'item_id' => 3,
                'current_quantity' => 95.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'stock_id' => 9,
                'lokasi_id' => 8, // SPBE Palembang Barat
                'item_id' => 1,
                'current_quantity' => 120.00,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('current_stocks')->insert($currentStocks);

        // Seed transaction_logs - memperbaiki agar hanya Pusat dan SPBE/BPT yang terlibat dalam transaksi
        $transactionLogs = [
            [
                'log_id' => 1,
                'tanggal_transaksi' => now()->subDays(25),
                'item_id' => 1,
                'tipe_pergerakan' => 'Penerimaan',
                'kuantitas' => 1000.00,
                'stok_akhir_sebelum' => 0.00,
                'stok_akhir_sesudah' => 1000.00,
                'lokasi_actor_id' => 1, // Pusat (P.Layang) - ID region
                'lokasi_tujuan_id' => null,
                'destination_sales_id' => null,
                'submission_id' => null,
                'keterangan' => 'Input stok awal',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'log_id' => 2,
                'tanggal_transaksi' => now()->subDays(20),
                'item_id' => 1,
                'tipe_pergerakan' => 'Penyaluran',
                'kuantitas' => 200.00,
                'stok_akhir_sebelum' => 1000.00,
                'stok_akhir_sesudah' => 800.00,
                'lokasi_actor_id' => 1, // Pusat (P.Layang) - ID region
                'lokasi_tujuan_id' => 1, // SPBE Jambi Utama - ID plant
                'destination_sales_id' => null,
                'submission_id' => null,
                'keterangan' => 'Penyaluran ke SPBE Jambi Utama',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'log_id' => 3,
                'tanggal_transaksi' => now()->subDays(15),
                'item_id' => 1,
                'tipe_pergerakan' => 'Penerimaan',
                'kuantitas' => 200.00,
                'stok_akhir_sebelum' => 0.00,
                'stok_akhir_sesudah' => 200.00,
                'lokasi_actor_id' => 1, // SPBE Jambi Utama - ID plant
                'lokasi_tujuan_id' => null,
                'destination_sales_id' => null,
                'submission_id' => null,
                'keterangan' => 'Penerimaan dari Pusat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'log_id' => 4,
                'tanggal_transaksi' => now()->subDays(18),
                'item_id' => 2,
                'tipe_pergerakan' => 'Penyaluran',
                'kuantitas' => 150.00,
                'stok_akhir_sebelum' => 500.00,
                'stok_akhir_sesudah' => 350.00,
                'lokasi_actor_id' => 1, // Pusat (P.Layang) - ID region
                'lokasi_tujuan_id' => 3, // SPBE Bengkulu Tengah - ID plant
                'destination_sales_id' => null,
                'submission_id' => null,
                'keterangan' => 'Penyaluran ke SPBE Bengkulu Tengah',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'log_id' => 5,
                'tanggal_transaksi' => now()->subDays(12),
                'item_id' => 2,
                'tipe_pergerakan' => 'Penerimaan',
                'kuantitas' => 150.00,
                'stok_akhir_sebelum' => 0.00,
                'stok_akhir_sesudah' => 150.00,
                'lokasi_actor_id' => 3, // SPBE Bengkulu Tengah - ID plant
                'lokasi_tujuan_id' => null,
                'destination_sales_id' => null,
                'submission_id' => null,
                'keterangan' => 'Penerimaan dari Pusat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'log_id' => 6,
                'tanggal_transaksi' => now()->subDays(10),
                'item_id' => 1,
                'tipe_pergerakan' => 'Penyaluran',
                'kuantitas' => 110.00,
                'stok_akhir_sebelum' => 200.00,
                'stok_akhir_sesudah' => 90.00,
                'lokasi_actor_id' => 1, // Pusat (P.Layang) - ID region
                'lokasi_tujuan_id' => 5, // SPBE Lampung Selatan - ID plant
                'destination_sales_id' => null,
                'submission_id' => null,
                'keterangan' => 'Penyaluran ke SPBE Lampung Selatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'log_id' => 7,
                'tanggal_transaksi' => now()->subDays(9),
                'item_id' => 1,
                'tipe_pergerakan' => 'Penerimaan',
                'kuantitas' => 110.00,
                'stok_akhir_sebelum' => 0.00,
                'stok_akhir_sesudah' => 110.00,
                'lokasi_actor_id' => 5, // SPBE Lampung Selatan - ID plant
                'lokasi_tujuan_id' => null,
                'destination_sales_id' => null,
                'submission_id' => null,
                'keterangan' => 'Penerimaan dari Pusat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'log_id' => 8,
                'tanggal_transaksi' => now()->subDays(5),
                'item_id' => 1,
                'tipe_pergerakan' => 'Penyaluran',
                'kuantitas' => 120.00,
                'stok_akhir_sebelum' => 150.00,
                'stok_akhir_sesudah' => 30.00,
                'lokasi_actor_id' => 1, // Pusat (P.Layang) - ID region
                'lokasi_tujuan_id' => 8, // SPBE Palembang Barat - ID plant
                'destination_sales_id' => null,
                'submission_id' => null,
                'keterangan' => 'Penyaluran ke SPBE Palembang Barat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'log_id' => 9,
                'tanggal_transaksi' => now()->subDays(4),
                'item_id' => 1,
                'tipe_pergerakan' => 'Penerimaan',
                'kuantitas' => 120.00,
                'stok_akhir_sebelum' => 0.00,
                'stok_akhir_sesudah' => 120.00,
                'lokasi_actor_id' => 8, // SPBE Palembang Barat - ID plant
                'lokasi_tujuan_id' => null,
                'destination_sales_id' => null,
                'submission_id' => null,
                'keterangan' => 'Penerimaan dari Pusat',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('transaction_logs')->insert($transactionLogs);

        // Seed destruction_submissions
        $destructionSubmissions = [
            [
                'submission_id' => 1,
                'no_surat' => 'UPP/001/2025',
                'tanggal_pengajuan' => now()->subDays(10),
                'tahapan' => 'Pengajuan Awal',
                'penanggung_jawab' => 'Budi Santoso',
                'item_id' => 3,
                'kuantitas_diajukan' => 20.00,
                'aktivitas_pemusnahan' => 'Pemusnahan regulator LPG yang tidak layak pakai',
                'keterangan_pengajuan' => 'Regulator dalam kondisi rusak berat',
                'status_pengajuan' => 'PROSES',
                'transaction_log_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'submission_id' => 2,
                'no_surat' => 'UPP/002/2025',
                'tanggal_pengajuan' => now()->subDays(5),
                'tahapan' => 'Proses Persetujuan',
                'penanggung_jawab' => 'Siti Aminah',
                'item_id' => 1,
                'kuantitas_diajukan' => 10.00,
                'aktivitas_pemusnahan' => 'Pemusnahan tabung LPG yang telah melewati masa pakai',
                'keterangan_pengajuan' => 'Tabung sudah tidak memenuhi standar keselamatan',
                'status_pengajuan' => 'PROSES',
                'transaction_log_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        
        DB::table('destruction_submissions')->insert($destructionSubmissions);
    }
}