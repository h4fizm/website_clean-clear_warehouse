<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // 1. TAMBAHKAN use statement ini

class Facility extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'kode_plant',
        'province',
        'regency',
        'type',
        'region_id',
    ];

    /**
     * Get the region that owns the Facility
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    // 2. TAMBAHKAN METHOD DI BAWAH INI
    /**
     * Mendapatkan semua item yang dimiliki oleh facility ini.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }
}