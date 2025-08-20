<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'facility_from',
        'facility_to',
        'jumlah',
        'jenis_transaksi',
        'no_surat_persetujuan',
        'no_ba_serah_terima',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    // Relasi ke Item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Asal fasilitas (nullable kalau dari luar)
    public function facilityFrom()
    {
        return $this->belongsTo(Facility::class, 'facility_from');
    }

    // Tujuan fasilitas (nullable kalau keluar sistem)
    public function facilityTo()
    {
        return $this->belongsTo(Facility::class, 'facility_to');
    }
}
