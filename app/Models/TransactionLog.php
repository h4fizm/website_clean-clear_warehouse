<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionLog extends Model
{
    use HasFactory;

    protected $primaryKey = 'log_id';
    protected $fillable = [
        'tanggal_transaksi',
        'item_id',
        'tipe_pergerakan',
        'kuantitas',
        'stok_akhir_sebelum',
        'stok_akhir_sesudah',
        'lokasi_actor_id',
        'lokasi_tujuan_id',
        'destination_sales_id',
        'submission_id',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'datetime',
        'kuantitas' => 'decimal:2',
        'stok_akhir_sebelum' => 'decimal:2',
        'stok_akhir_sesudah' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function destinationSale()
    {
        return $this->belongsTo(DestinationSale::class, 'destination_sales_id');
    }

    public function destructionSubmission()
    {
        return $this->belongsTo(DestructionSubmission::class, 'submission_id');
    }

    public function getActorLocationAttribute()
    {
        // Coba cari di plants dulu
        $plant = Plant::find($this->lokasi_actor_id);
        if ($plant) {
            return $plant;
        }

        // Jika tidak ada di plants, cari di regions
        return Region::find($this->lokasi_actor_id);
    }

    public function getTargetLocationAttribute()
    {
        if (!$this->lokasi_tujuan_id) {
            return null;
        }

        // Coba cari di plants dulu
        $plant = Plant::find($this->lokasi_tujuan_id);
        if ($plant) {
            return $plant;
        }

        // Jika tidak ada di plants, cari di regions
        return Region::find($this->lokasi_tujuan_id);
    }

    public function scopeByMovementType($query, $type)
    {
        return $query->where('tipe_pergerakan', $type);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal_transaksi', [$startDate, $endDate]);
    }
}