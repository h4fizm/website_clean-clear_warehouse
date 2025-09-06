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
            $table->id(); // Kolom ID standar

            // Kolom ini akan menyimpan nama dasar material, contoh: 'Tabung LPG 3 Kg'
            // Dibuat unik agar tidak ada duplikasi data kapasitas untuk material yang sama.
            $table->string('material_base_name')->unique();

            // Kolom ini untuk menyimpan angka kapasitasnya.
            // Dibuat unsigned agar nilainya tidak bisa negatif.
            $table->unsignedInteger('capacity')->default(0);

            $table->timestamps(); // Kolom created_at dan updated_at
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
