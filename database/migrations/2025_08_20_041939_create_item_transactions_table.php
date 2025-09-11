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
            $table->unsignedBigInteger('user_id')->nullable();

            // Kolom Asal & Tujuan
            $table->unsignedBigInteger('facility_from')->nullable();
            $table->unsignedBigInteger('facility_to')->nullable();
            $table->unsignedBigInteger('region_from')->nullable();
            $table->unsignedBigInteger('region_to')->nullable();
            $table->string('tujuan_sales')->nullable();

            // Kolom Kuantitas & Jenis Transaksi
            $table->integer('jumlah');
            $table->integer('stok_awal_asal')->nullable()->comment('Stok asal SEBELUM transaksi');
            $table->integer('stok_akhir_asal')->nullable()->comment('Stok asal SETELAH transaksi');

            // ✅ DITAMBAHKAN: Kolom untuk mencatat stok tujuan
            $table->integer('stok_awal_tujuan')->nullable()->comment('Stok tujuan SEBELUM transaksi');
            $table->integer('stok_akhir_tujuan')->nullable()->comment('Stok tujuan SETELAH transaksi');

            $table->enum('jenis_transaksi', ['penerimaan', 'penyaluran', 'transfer', 'sales', 'pemusnahan']);
            $table->string('tahapan')->nullable();
            $table->string('status')->default('proses');

            // Kolom Dokumen & Timestamps
            $table->string('no_surat_persetujuan', 100)->nullable();
            $table->string('no_ba_serah_terima', 100)->nullable();
            $table->text('keterangan_transaksi')->nullable();
            $table->string('penanggungjawab', 255)->nullable();

            // Kolom Tambahan untuk Pemusnahan
            $table->date('tanggal_pemusnahan')->nullable();
            $table->text('aktivitas_pemusnahan')->nullable();

            $table->timestamps();

            // Definisi Foreign Keys
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('facility_from')->references('id')->on('facilities')->onDelete('set null');
            $table->foreign('facility_to')->references('id')->on('facilities')->onDelete('set null');
            $table->foreign('region_from')->references('id')->on('regions')->onDelete('set null');
            $table->foreign('region_to')->references('id')->on('regions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_transactions');
    }
};