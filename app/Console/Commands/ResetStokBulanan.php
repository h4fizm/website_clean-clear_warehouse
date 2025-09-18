<?php

// namespace App\Console\Commands;

// use Illuminate\Console\Command;
// use Illuminate\Support\Facades\DB;

// class ResetStokBulanan extends Command
// {
    
//     protected $signature = 'stok:reset-bulanan';

   
//     protected $description = 'Reset stok_awal menjadi 0 dan normalkan stok_akhir setiap awal bulan';

    
//     public function handle()
//     {

//         DB::table('items')
//             ->where('is_active', 1)
//             ->update([
//                 'stok_akhir' => DB::raw("
//                     CASE 
//                         WHEN stok_akhir = 0 AND stok_awal > 0 THEN stok_awal
//                         ELSE stok_akhir
//                     END
//                 "),
//                 'stok_awal' => 0,
//                 'updated_at' => now(),
//             ]);

//         $this->info('✅ Reset stok bulanan berhasil dijalankan.');
//     }
// }
