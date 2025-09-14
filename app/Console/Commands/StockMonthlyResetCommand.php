<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\ItemTransaction;
use Carbon\Carbon;

// class StockMonthlyResetCommand extends Command
// {
//     protected $signature = 'stock:monthly-reset';

//     protected $description = 'Reset stok awal bulanan dan hapus data transaksi bulan sebelumnya.';

//     public function handle()
//     {
//         $this->info('Memulai proses reset stok bulanan...');
//         Log::info('Memulai proses reset stok bulanan...');

//         $startOfPreviousMonth = Carbon::now()->subMonth()->startOfMonth();
//         $endOfPreviousMonth = Carbon::now()->subMonth()->endOfMonth();
//         $startOfCurrentMonth = Carbon::now()->startOfMonth();

//         try {
//             DB::transaction(function () use ($startOfPreviousMonth, $endOfPreviousMonth, $startOfCurrentMonth) { // <-- PERBAIKAN DI SINI

//                 $items = Item::all();
//                 $this->info('Menghitung ulang stok untuk ' . $items->count() . ' item...');

//                 foreach ($items as $item) {
//                     // Logika di dalam perulangan ini sudah benar, tidak perlu diubah
//                     $penerimaan = ItemTransaction::whereHas('item', fn($q) => $q->where('kode_material', $item->kode_material))
//                         ->where(function ($query) use ($item) {
//                             $query->where('facility_to', $item->facility_id)
//                                 ->orWhere('region_to', $item->region_id);
//                         })
//                         ->whereBetween('created_at', [$startOfPreviousMonth, $endOfPreviousMonth])
//                         ->sum('jumlah');

//                     $penyaluran = $item->outgoingTransfers()
//                         ->whereBetween('created_at', [$startOfPreviousMonth, $endOfPreviousMonth])
//                         ->sum('jumlah');

//                     $sales = $item->transactions()
//                         ->where('jenis_transaksi', 'sales')
//                         ->whereBetween('created_at', [$startOfPreviousMonth, $endOfPreviousMonth])
//                         ->sum('jumlah');

//                     $stokAkhirFinal = $item->stok_awal + $penerimaan - $penyaluran - $sales;

//                     $item->stok_awal = $stokAkhirFinal;
//                     $item->stok_akhir = $stokAkhirFinal;
//                     $item->save();
//                 }

//                 $this->info('Update stok awal selesai.');
//                 Log::info('Update stok awal untuk semua item telah selesai.');

//                 $this->info('Menghapus data transaksi lama...');
//                 $deletedRows = ItemTransaction::where('created_at', '<', $startOfCurrentMonth)->delete();
//                 $this->info("{$deletedRows} baris data transaksi lama berhasil dihapus.");
//                 Log::info("{$deletedRows} baris data transaksi lama berhasil dihapus.");

//             });

//             $this->info('Proses reset stok bulanan berhasil diselesaikan.');
//             Log::info('Proses reset stok bulanan berhasil diselesaikan.');

//         } catch (\Exception $e) {
//             $this->error('Terjadi kesalahan: ' . $e->getMessage());
//             Log::error('Gagal melakukan reset stok bulanan: ' . $e->getMessage());
//         }
//     }
// }