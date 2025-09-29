<?php

namespace App\Console;

use App\Console\Commands\PerformMonthlyStockReset;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule): void
    {
        // Daftarkan command untuk berjalan setiap bulan pada hari pertama jam 1 pagi
        // $schedule->command(PerformMonthlyStockReset::class)->monthlyOn(1, '01:00');
        $schedule->command(PerformMonthlyStockReset::class)->monthlyOn(1, '01:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
