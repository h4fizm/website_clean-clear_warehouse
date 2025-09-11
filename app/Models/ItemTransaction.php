<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransaction extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $fillable = [
        'item_id',
        'user_id',
        'facility_from',
        'facility_to',
        'region_from',
        'region_to',
        'jumlah',
        'stok_awal_asal',
        'stok_akhir_asal',
        // ✅ Perbaikan: Tambahkan kolom-kolom ini ke dalam fillable
        'stok_awal_tujuan',
        'stok_akhir_tujuan',
        'jenis_transaksi',
        'tujuan_sales',
        'no_surat_persetujuan',
        'no_ba_serah_terima',
        'keterangan_transaksi',
        'tahapan',
        'status',
        'tanggal_pemusnahan',
        'aktivitas_pemusnahan',
        'penanggungjawab',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONSHIPS
    |--------------------------------------------------------------------------
    */

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function facilityFrom()
    {
        return $this->belongsTo(Facility::class, 'facility_from');
    }

    public function facilityTo()
    {
        return $this->belongsTo(Facility::class, 'facility_to');
    }

    public function regionFrom()
    {
        return $this->belongsTo(Region::class, 'region_from');
    }

    public function regionTo()
    {
        return $this->belongsTo(Region::class, 'region_to');
    }
}