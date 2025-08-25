<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id', // <-- Tambahkan ini
        'facility_from',
        'facility_to',
        'region_from',
        'region_to',
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

    // Relasi ke User (Penanggung Jawab)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Asal facility
    public function facilityFrom()
    {
        return $this->belongsTo(Facility::class, 'facility_from');
    }

    // Tujuan facility
    public function facilityTo()
    {
        return $this->belongsTo(Facility::class, 'facility_to');
    }

    // Asal region (khusus P. Layang)
    public function regionFrom()
    {
        return $this->belongsTo(Region::class, 'region_from');
    }

    // Tujuan region (khusus P. Layang)
    public function regionTo()
    {
        return $this->belongsTo(Region::class, 'region_to');
    }
}