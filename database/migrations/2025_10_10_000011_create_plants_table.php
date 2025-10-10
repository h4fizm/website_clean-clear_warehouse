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
        Schema::create('plants', function (Blueprint $table) {
            $table->id('plant_id'); // ID unik Plant
            $table->foreignId('region_id')->constrained('regions', 'region_id'); // Terhubung ke regions.region_id
            $table->string('nama_plant', 100); // Nama lengkap Plant
            $table->string('kode_plant', 20); // Kode unik Plant
            $table->enum('kategori_plant', ['SPBE', 'BPT']); // Jenis plant
            $table->string('provinsi', 50); // Provinsi lokasi plant
            $table->string('kabupaten', 50); // Kabupaten/kota lokasi plant
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plants');
    }
};