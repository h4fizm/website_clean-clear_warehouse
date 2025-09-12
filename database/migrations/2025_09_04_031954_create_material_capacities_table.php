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
        Schema::create('material_capacities', function (Blueprint $table) {
            $table->id();

            // Kolom ini akan menyimpan nama dasar material, contoh: 'Tabung LPG 3 Kg'
            $table->string('material_base_name');

            // Kolom ini untuk menyimpan angka kapasitasnya.
            $table->unsignedInteger('capacity')->default(0);

            // ✅ TAMBAHAN: Kolom untuk menyimpan bulan dan tahun.
            $table->unsignedTinyInteger('month');
            $table->year('year');

            // ✅ PERBAIKAN: Buat kombinasi unik untuk mencegah duplikasi.
            $table->unique(['material_base_name', 'month', 'year'], 'material_capacity_unique');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_capacities');
    }
};