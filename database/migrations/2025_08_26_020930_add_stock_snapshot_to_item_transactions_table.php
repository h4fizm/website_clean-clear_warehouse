<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Perintah ini untuk MEMODIFIKASI tabel yang sudah ada
        Schema::table('item_transactions', function (Blueprint $table) {
            // Menambahkan kolom untuk stok awal dari lokasi asal
            $table->integer('stok_awal_asal')->after('jumlah')->nullable()->comment('Stok item di lokasi asal SEBELUM transaksi');

            // Menambahkan kolom untuk stok akhir dari lokasi asal
            $table->integer('stok_akhir_asal')->after('stok_awal_asal')->nullable()->comment('Stok item di lokasi asal SETELAH transaksi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Perintah ini untuk MENGHAPUS kolom jika migrasi di-rollback
        Schema::table('item_transactions', function (Blueprint $table) {
            $table->dropColumn(['stok_awal_asal', 'stok_akhir_asal']);
        });
    }
};