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
        Schema::create('destination_sales', function (Blueprint $table) {
            $table->id('destination_id'); // ID tujuan
            $table->string('nama_tujuan', 50); // Jenis tujuan pengiriman
            $table->string('keterangan', 100)->nullable(); // (Opsional) Keterangan tambahan
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destination_sales');
    }
};