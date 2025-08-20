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
        Schema::create('item_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('facility_from')->nullable();
            $table->unsignedBigInteger('facility_to')->nullable();
            $table->unsignedBigInteger('region_from')->nullable();
            $table->unsignedBigInteger('region_to')->nullable();
            $table->integer('jumlah');
            $table->enum('jenis_transaksi', ['penerimaan', 'penyaluran', 'transfer']);
            $table->string('no_surat_persetujuan', 100)->nullable();
            $table->string('no_ba_serah_terima', 100)->nullable();
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('facility_from')->references('id')->on('facilities')->onDelete('set null');
            $table->foreign('facility_to')->references('id')->on('facilities')->onDelete('set null');
            $table->foreign('region_from')->references('id')->on('regions')->onDelete('set null');
            $table->foreign('region_to')->references('id')->on('regions')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('item_transactions');
    }
};
