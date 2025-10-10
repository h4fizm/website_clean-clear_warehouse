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
        // Tambahkan foreign key untuk destruction_submissions.transaction_log_id
        Schema::table('destruction_submissions', function (Blueprint $table) {
            $table->foreign('transaction_log_id')->references('log_id')->on('transaction_logs')->onDelete('set null');
        });
        
        // Tambahkan foreign key untuk transaction_logs.submission_id
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->foreign('submission_id')->references('submission_id')->on('destruction_submissions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('destruction_submissions', function (Blueprint $table) {
            $table->dropForeign(['transaction_log_id']);
        });
        
        Schema::table('transaction_logs', function (Blueprint $table) {
            $table->dropForeign(['submission_id']);
        });
    }
};