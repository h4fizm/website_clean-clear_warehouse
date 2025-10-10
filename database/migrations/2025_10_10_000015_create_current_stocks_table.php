<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('current_stocks', function (Blueprint $table) {
            $table->id('stock_id'); // ID stok unik
            $table->unsignedBigInteger('lokasi_id'); // ID dari lokasi (bisa region atau plant)
            $table->foreignId('item_id')->constrained('items', 'item_id'); // Material terkait
            $table->decimal('current_quantity', 10, 2); // Saldo stok terakhir
            $table->timestamps();
            
            // Tambahkan indeks untuk mendukung pencarian berdasarkan lokasi
            $table->index('lokasi_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('current_stocks');
    }
};