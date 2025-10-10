<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InitialStock extends Model
{
    use HasFactory;

    protected $primaryKey = 'initial_stock_id';
    protected $fillable = [
        'item_id',
        'quantity',
        'tanggal_masuk',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'tanggal_masuk' => 'date',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}