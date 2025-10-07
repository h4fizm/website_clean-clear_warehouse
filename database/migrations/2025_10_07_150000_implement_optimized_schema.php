<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations to fully implement the optimized database schema.
     * This migration will:
     * 1. Create the new optimized transaction tables
     * 2. Keep the old table as a view or for data migration
     * 3. Update the application to use the new optimized structure
     */
    public function up(): void
    {
        // Create the optimized transaction tables
        Schema::create('base_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedBigInteger('user_id')->nullable();
            
            // Source & Destination
            $table->unsignedBigInteger('facility_from')->nullable();
            $table->unsignedBigInteger('facility_to')->nullable();
            $table->unsignedBigInteger('region_from')->nullable();
            $table->unsignedBigInteger('region_to')->nullable();
            
            // Quantities
            $table->integer('jumlah');
            $table->integer('stok_awal_asal')->nullable()->comment('Stok asal SEBELUM transaksi');
            $table->integer('stok_akhir_asal')->nullable()->comment('Stok asal SETELAH transaksi');
            $table->integer('stok_awal_tujuan')->nullable()->comment('Stok tujuan SEBELUM transaksi');
            $table->integer('stok_akhir_tujuan')->nullable()->comment('Stok tujuan SETELAH transaksi');
            
            $table->enum('jenis_transaksi', ['penerimaan', 'penyaluran', 'transfer', 'sales', 'pemusnahan']);
            $table->string('tahapan')->nullable();
            $table->string('status')->default('proses');
            
            // Documentation
            $table->string('no_surat_persetujuan', 100)->nullable();
            $table->string('no_ba_serah_terima', 100)->nullable();
            $table->text('keterangan_transaksi')->nullable();
            
            $table->timestamps();
            
            // Foreign Keys
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('facility_from')->references('id')->on('facilities')->onDelete('set null');
            $table->foreign('facility_to')->references('id')->on('facilities')->onDelete('set null');
            $table->foreign('region_from')->references('id')->on('regions')->onDelete('set null');
            $table->foreign('region_to')->references('id')->on('regions')->onDelete('set null');
        });
        
        // Create specialized transaction tables
        Schema::create('transfer_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('base_transaction_id');
            $table->timestamps();
            
            // Foreign Key
            $table->foreign('base_transaction_id')->references('id')->on('base_transactions')->onDelete('cascade');
        });
        
        Schema::create('sales_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('base_transaction_id');
            $table->string('tujuan_sales')->nullable();
            $table->timestamps();
            
            // Foreign Key
            $table->foreign('base_transaction_id')->references('id')->on('base_transactions')->onDelete('cascade');
        });
        
        Schema::create('pemusnahan_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('base_transaction_id');
            $table->date('tanggal_pemusnahan')->nullable();
            $table->text('aktivitas_pemusnahan')->nullable();
            $table->string('penanggungjawab', 255)->nullable();
            $table->timestamps();
            
            // Foreign Key
            $table->foreign('base_transaction_id')->references('id')->on('base_transactions')->onDelete('cascade');
        });
        
        Schema::create('penerimaan_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('base_transaction_id');
            $table->timestamps();
            
            // Foreign Key
            $table->foreign('base_transaction_id')->references('id')->on('base_transactions')->onDelete('cascade');
        });
        
        Schema::create('penyaluran_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('base_transaction_id');
            $table->timestamps();
            
            // Foreign Key
            $table->foreign('base_transaction_id')->references('id')->on('base_transactions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penyaluran_transactions');
        Schema::dropIfExists('penerimaan_transactions');
        Schema::dropIfExists('pemusnahan_transactions');
        Schema::dropIfExists('sales_transactions');
        Schema::dropIfExists('transfer_transactions');
        Schema::dropIfExists('base_transactions');
    }
};