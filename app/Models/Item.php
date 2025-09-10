<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * Relasi ke Facility (jika item dimiliki SPBE/BPT).
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Relasi ke Region (khusus untuk P. Layang/Pusat).
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Relasi ke semua transaksi yang terkait langsung dengan item ini.
     * Ini bisa berupa transaksi keluar (penyaluran) atau sales.
     */
    public function transactions()
    {
        return $this->hasMany(ItemTransaction::class, 'item_id');
    }

    /**
     * Relasi untuk transaksi yang MENGIRIM ke item ini (penerimaan).
     * Relasi ini akan mencari transaksi yang memiliki 'region_to' atau 'facility_to' yang cocok.
     */
    public function incomingTransfers()
    {
        // Jika item ini adalah item Pusat
        if (is_null($this->facility_id)) {
            return ItemTransaction::where('region_to', $this->region_id)
                ->whereNull('facility_to');
        }

        // Jika item ini adalah item Fasilitas
        return ItemTransaction::where('facility_to', $this->facility_id);
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Hitung stok akhir berdasarkan stok awal dan semua transaksi terkait.
     * Ini digunakan sebagai fallback jika data tidak di-load dari controller.
     */
    public function getStokAkhirAttribute()
    {
        // Perhitungan yang lebih sederhana dan fokus pada transaksi yang mempengaruhi item ini
        $totalMasuk = $this->incomingTransfers()->where('jenis_transaksi', 'transfer')->sum('jumlah');
        $totalKeluar = $this->transactions()->whereIn('jenis_transaksi', ['transfer', 'sales', 'pemusnahan'])->sum('jumlah');

        return $this->stok_awal + $totalMasuk - $totalKeluar;
    }
}