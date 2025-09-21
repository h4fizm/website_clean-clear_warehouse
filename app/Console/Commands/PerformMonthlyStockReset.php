<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Item;
use Illuminate\Support\Facades\Log;

class PerformMonthlyStockReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:reset-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets the stok_awal to the current stok_akhir for all items at the beginning of the month.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting monthly stock reset...');
        Log::info('Starting monthly stock reset...');

        try {
            // This is the core logic: Set the initial stock for the new month
            // to be the final stock of the previous month.
            $affectedRows = Item::query()->update(['stok_awal' => DB::raw('stok_akhir')]);

            $this->info("Monthly stock reset complete. {$affectedRows} items updated.");
            Log::info("Monthly stock reset complete. {$affectedRows} items updated.");

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Monthly stock reset failed: ' . $e->getMessage());
            Log::error('Monthly stock reset failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
