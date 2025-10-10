<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestructionSubmission extends Model
{
    use HasFactory;

    protected $primaryKey = 'submission_id';
    protected $fillable = [
        'no_surat',
        'tanggal_pengajuan',
        'tahapan',
        'penanggung_jawab',
        'item_id',
        'kuantitas_diajukan',
        'aktivitas_pemusnahan',
        'keterangan_pengajuan',
        'status_pengajuan',
        'transaction_log_id',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'kuantitas_diajukan' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function transactionLog()
    {
        return $this->belongsTo(TransactionLog::class, 'transaction_log_id');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status_pengajuan', $status);
    }

    public function scopeByStage($query, $stage)
    {
        return $query->where('tahapan', $stage);
    }

    public function isCompleted()
    {
        return $this->status_pengajuan === 'DONE';
    }

    public function isRejected()
    {
        return $this->status_pengajuan === 'DITOLAK';
    }

    public function isPending()
    {
        return $this->status_pengajuan === 'PROSES';
    }
}