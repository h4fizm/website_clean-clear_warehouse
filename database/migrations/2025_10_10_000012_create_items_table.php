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
        Schema::create('items', function (Blueprint $table) {
            $table->id('item_id'); // ID unik material
            $table->string('nama_material', 100); // Nama material
            $table->string('kode_material', 20); // Kode unik material
            $table->enum('kategori_material', ['Baik', 'Baru', 'Rusak', 'Afkir']); // Kondisi material
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};