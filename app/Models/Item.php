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
        'kategori_material', // BARU: Tambahkan ini
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
     * Relasi umum ke semua transaksi yang tercatat di item_id ini.
     * Secara default, ini mewakili transaksi KELUAR (penyaluran).
     */
    public function transactions()
    {
        return $this->hasMany(ItemTransaction::class);
    }

    /**
     * [BARU] Relasi spesifik untuk transaksi KELUAR (Penyaluran) dari item ini.
     * Sama seperti transactions(), namun dengan nama yang lebih jelas.
     */
    public function outgoingTransfers()
    {
        return $this->hasMany(ItemTransaction::class, 'item_id');
    }

    /**
     * [BARU] Relasi spesifik untuk transaksi MASUK (Penerimaan) ke lokasi item ini.
     * Bekerja dengan mencocokkan 'region_to' atau 'facility_to' di tabel transaksi.
     */
    public function incomingTransfers()
    {
        // Jika ini item Pusat (tidak punya facility_id)
        if (is_null($this->facility_id)) {
            return $this->hasMany(ItemTransaction::class, 'region_to', 'region_id')
                ->whereNull('facility_to');
        }

        // Jika ini item Facility
        return $this->hasMany(ItemTransaction::class, 'facility_to', 'facility_id');
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & HELPERS
    |--------------------------------------------------------------------------
    */
    public function getStokAkhirAttribute()
    {
        if (
            array_key_exists('penerimaan_total', $this->attributes) &&
            array_key_exists('penyaluran_total', $this->attributes) &&
            array_key_exists('sales_total', $this->attributes)
        ) {
            // Gunakan nilai dari controller (lebih efisien)
            $penerimaan = (int) $this->penerimaan_total;
            $penyaluran = (int) $this->penyaluran_total;
            $sales = (int) $this->sales_total;
        } else {
            // Hitung manual kalau tidak ada preload dari controller
            $penyaluran = $this->outgoingTransfers()->sum('jumlah');

            // Penerimaan (logika lama tetap dipakai)
            $penerimaan = 0;
            if (is_null($this->facility_id)) {
                $penerimaan = ItemTransaction::where('region_to', $this->region_id)
                    ->whereNull('facility_to')
                    ->whereHas('item', fn($q) => $q->where('kode_material', $this->kode_material))
                    ->sum('jumlah');
            } else {
                $penerimaan = ItemTransaction::where('facility_to', $this->facility_id)
                    ->whereHas('item', fn($q) => $q->where('kode_material', $this->kode_material))
                    ->sum('jumlah');
            }

            // Tambahkan sales
            $sales = $this->transactions()->where('jenis_transaksi', 'sales')->sum('jumlah');
        }

        // Perhitungan stok dengan logika pemusnahan jika ada
        $pemusnahan = $this->transactions()->where('jenis_transaksi', 'pemusnahan')->sum('jumlah');

        return $this->stok_awal + $penerimaan - $penyaluran - $sales - $pemusnahan;
    }
}
