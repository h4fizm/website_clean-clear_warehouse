<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'facility_id',
        'region_id',
        'is_active', // ✅ Kolom 'is_active' ditambahkan di sini
        'nama_material',
        'kode_material',
        'kategori_material',
        'stok_awal',
        'stok_akhir',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function baseTransactions()
    {
        return $this->hasMany(ItemTransaction::class, 'item_id');
    }
    
    public function transactions()
    {
        return $this->hasMany(ItemTransaction::class, 'item_id');
    }
}