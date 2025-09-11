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

    public function transactions()
    {
        return $this->hasMany(ItemTransaction::class, 'item_id');
    }

    // Perbaikan: Hapus relasi incomingTransfers() yang membingungkan.
    // Relasi 'transactions' sudah cukup untuk semua perhitungan.

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & HELPERS
    |--------------------------------------------------------------------------
    */

    // Perbaikan: Hapus accessor ini karena perhitungannya tidak akurat.
    // Kita akan melakukan perhitungan di Controller untuk memastikan data yang ditampilkan selalu akurat.
    // Jika Anda benar-benar membutuhkan ini, logika yang benar akan jauh lebih kompleks
    // dan harus mencakup semua jenis transaksi yang memengaruhi stok.
}