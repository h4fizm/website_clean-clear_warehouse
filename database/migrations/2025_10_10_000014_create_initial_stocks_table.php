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
        Schema::create('initial_stocks', function (Blueprint $table) {
            $table->id('initial_stock_id'); // ID stok awal
            $table->foreignId('item_id')->constrained('items', 'item_id'); // Material terkait
            $table->decimal('quantity', 10, 2); // Jumlah awal
            $table->date('tanggal_masuk'); // Tanggal input stok awal
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('initial_stocks');
    }
};