<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentStock extends Model
{
    use HasFactory;

    protected $primaryKey = 'stock_id';
    protected $fillable = [
        'lokasi_id',
        'item_id',
        'current_quantity',
    ];

    protected $casts = [
        'current_quantity' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function getLocationAttribute()
    {
        // Coba cari di plants dulu
        $plant = Plant::find($this->lokasi_id);
        if ($plant) {
            return $plant;
        }

        // Jika tidak ada di plants, cari di regions
        return Region::find($this->lokasi_id);
    }

    public function scopeByLocation($query, $locationId)
    {
        return $query->where('lokasi_id', $locationId);
    }
}