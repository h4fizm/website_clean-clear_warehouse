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
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id('log_id'); // ID log unik
            $table->dateTime('tanggal_transaksi'); // Waktu kejadian transaksi
            $table->foreignId('item_id')->constrained('items', 'item_id'); // Material yang diproses
            $table->enum('tipe_pergerakan', ['Penerimaan', 'Penyaluran', 'Transaksi Sales', 'Pemusnahan']); // Jenis aktivitas transaksi
            $table->decimal('kuantitas', 10, 2); // Jumlah stok yang bergerak
            $table->decimal('stok_akhir_sebelum', 10, 2); // Saldo sebelum transaksi
            $table->decimal('stok_akhir_sesudah', 10, 2); // Saldo setelah transaksi
            $table->unsignedBigInteger('lokasi_actor_id'); // ID lokasi asal
            $table->unsignedBigInteger('lokasi_tujuan_id')->nullable(); // ID lokasi tujuan (jika relevan)
            $table->unsignedBigInteger('destination_sales_id')->nullable(); // Tujuan pengiriman sales
            $table->unsignedBigInteger('submission_id')->nullable(); // ID pengajuan pemusnahan
            $table->string('keterangan', 100)->nullable(); // (Opsional) Catatan tambahan (misal nomor surat)
            $table->timestamps();
            
            // Tambahkan foreign key untuk destination_sales_id
            $table->foreign('destination_sales_id')->references('destination_id')->on('destination_sales')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_logs');
    }
};