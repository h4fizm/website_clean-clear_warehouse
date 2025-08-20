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
        'stok_awal',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Relasi ke Facility (jika item dimiliki SPBE/BPT)
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    // Relasi ke Region (khusus untuk P. Layang)
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    // Relasi ke Transaksi Item
    public function transactions()
    {
        return $this->hasMany(ItemTransaction::class);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & HELPERS
    |--------------------------------------------------------------------------
    */

    // Hitung stok akhir langsung dari relasi transaksi
    public function getStokAkhirAttribute()
    {
        $penerimaan = $this->transactions()
            ->where('jenis_transaksi', 'penerimaan')
            ->sum('jumlah');

        $penyaluran = $this->transactions()
            ->where('jenis_transaksi', 'penyaluran')
            ->sum('jumlah');

        return $this->stok_awal + $penerimaan - $penyaluran;
    }
}
