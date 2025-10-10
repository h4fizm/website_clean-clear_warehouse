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
        Schema::create('destruction_submissions', function (Blueprint $table) {
            $table->id('submission_id'); // ID pengajuan
            $table->string('no_surat', 50); // Nomor surat resmi pengajuan
            $table->date('tanggal_pengajuan'); // Tanggal pengajuan
            $table->string('tahapan', 50); // Status tahapan proses (misal: "Pengajuan Awal")
            $table->string('penanggung_jawab', 100); // Nama penanggung jawab
            $table->foreignId('item_id')->constrained('items', 'item_id'); // Material yang diajukan untuk dimusnahkan
            $table->decimal('kuantitas_diajukan', 10, 2); // Jumlah material yang diajukan
            $table->string('aktivitas_pemusnahan', 255); // Deskripsi aktivitas pemusnahan
            $table->text('keterangan_pengajuan')->nullable(); // Catatan tambahan
            $table->enum('status_pengajuan', ['PROSES', 'DONE', 'DITOLAK'])->default('PROSES'); // Status pengajuan
            $table->unsignedBigInteger('transaction_log_id')->nullable(); // Diisi otomatis saat status = DONE
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('destruction_submissions');
    }
};