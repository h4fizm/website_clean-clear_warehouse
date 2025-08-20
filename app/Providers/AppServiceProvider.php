<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Tambahkan baris ini
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Tambahkan baris ini untuk mengatur Carbon ke Bahasa Indonesia
        Carbon::setLocale('id');
    }
}