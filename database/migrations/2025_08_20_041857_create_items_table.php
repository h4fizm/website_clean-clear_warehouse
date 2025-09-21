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
        Schema::create('items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('facility_id')->nullable();
            $table->unsignedBigInteger('region_id')->nullable();
            // ✅ Kolom 'is_active' ditambahkan di sini
            $table->boolean('is_active')->default(true);
            $table->string('nama_material', 150);
            $table->string('kode_material', 50);
            $table->string('kategori_material', 50);
            $table->integer('stok_awal')->default(0);
            $table->integer('stok_akhir')->default(0);

            $table->timestamps();

            $table->foreign('facility_id')->references('id')->on('facilities')->onDelete('cascade');
            $table->foreign('region_id')->references('id')->on('regions')->onDelete('cascade');
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