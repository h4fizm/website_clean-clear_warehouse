<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItemTransaction;
use App\Models\Item;
use App\Models\User;
use App\Models\Facility;
use App\Models\Region;
use App\Models\TransferTransaction;
use App\Models\SalesTransaction;
use App\Models\PemusnahanTransaction;
use App\Models\PenerimaanTransaction;
use App\Models\PenyaluranTransaction;

class BaseTransactionsSeeder extends Seeder
{
    public function run()
    {
        $items = Item::all();
        $users = User::all();
        $facilities = Facility::all();
        $regions = Region::all();
        
        $transactionTypes = ['penerimaan', 'penyaluran', 'transfer', 'sales', 'pemusnahan'];
        
        // Create 50 realistic transactions
        for ($i = 0; $i < 50; $i++) {
            $item = $items->random();
            $user = $users->isNotEmpty() ? $users->random() : null;
            $type = $transactionTypes[array_rand($transactionTypes)];
            $status = ['proses', 'selesai', 'pending'][array_rand(['proses', 'selesai', 'pending'])];
            $jumlah = rand(10, 200);
            
            $baseTransaction = ItemTransaction::create([
                'item_id' => $item->id,
                'user_id' => $user ? $user->id : null,
                'facility_from' => $facilities->random()->id,
                'facility_to' => $facilities->random()->id,
                'region_from' => $regions->random()->id,
                'region_to' => $regions->random()->id,
                'jumlah' => $jumlah,
                'stok_awal_asal' => rand(100, 1000),
                'stok_akhir_asal' => rand(50, 800),
                'stok_awal_tujuan' => rand(100, 1000),
                'stok_akhir_tujuan' => rand(150, 1200),
                'jenis_transaksi' => $type,
                'tahapan' => 'Tahapan ' . ($i + 1),
                'status' => $status,
                'no_surat_persetujuan' => 'SP-' . rand(1000, 9999) . '/2025',
                'no_ba_serah_terima' => 'BA-' . rand(1000, 9999) . '/2025',
                'keterangan_transaksi' => 'Keterangan transaksi untuk ' . $item->nama_material,
            ]);
            
            // Create specialized transaction based on type
            switch ($type) {
                case 'transfer':
                    TransferTransaction::create([
                        'base_transaction_id' => $baseTransaction->id,
                    ]);
                    break;
                case 'sales':
                    SalesTransaction::create([
                        'base_transaction_id' => $baseTransaction->id,
                        'tujuan_sales' => 'PT. Jaya Abadi ' . rand(1, 50),
                    ]);
                    break;
                case 'pemusnahan':
                    PemusnahanTransaction::create([
                        'base_transaction_id' => $baseTransaction->id,
                        'tanggal_pemusnahan' => now()->subDays(rand(1, 30))->toDateString(),
                        'aktivitas_pemusnahan' => 'Pemusnahan rutin untuk ' . $item->nama_material,
                        'penanggungjawab' => 'John Doe ' . ($i + 1),
                    ]);
                    break;
                case 'penerimaan':
                    PenerimaanTransaction::create([
                        'base_transaction_id' => $baseTransaction->id,
                    ]);
                    break;
                case 'penyaluran':
                    PenyaluranTransaction::create([
                        'base_transaction_id' => $baseTransaction->id,
                    ]);
                    break;
            }
        }
    }
}