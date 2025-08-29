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
        'stok_awal',
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

    /**
     * [DIPERBAIKI] Hitung stok akhir berdasarkan transaksi 'transfer'.
     * Accessor ini sekarang cerdas: ia akan menggunakan data dari controller jika ada,
     * atau menghitung manual jika dipanggil langsung.
     */
    public function getStokAkhirAttribute()
    {
        // Cek apakah total sudah dihitung oleh controller (lewat withSum).
        // Ini adalah cara paling efisien untuk halaman daftar/index.
        if (array_key_exists('penerimaan_total', $this->attributes) && array_key_exists('penyaluran_total', $this->attributes)) {
            // Gunakan nilai yang sudah di-load dari controller
            $penerimaan = (int) $this->penerimaan_total;
            $penyaluran = (int) $this->penyaluran_total;
        } else {
            // Jika tidak, hitung manual (fallback, lebih lambat tapi tetap akurat).
            // Ini berguna jika Anda memanggil $item->stok_akhir di tempat lain.

            // Hitung total barang keluar (penyaluran)
            $penyaluran = $this->outgoingTransfers()->sum('jumlah');

            // =================== LOGIKA PENERIMAAN DIPERBAIKI ===================
            $penerimaan = 0;
            if (is_null($this->facility_id)) {
                // Untuk item PUSAT: cari transaksi yang tujuannya adalah region pusat ini
                // DAN kode materialnya cocok.
                $penerimaan = ItemTransaction::where('region_to', $this->region_id)
                    ->whereNull('facility_to') // <-- Tambahan penting: pastikan tujuannya adalah PUSAT, bukan facility
                    ->whereHas('item', function ($query) {
                        $query->where('kode_material', $this->kode_material);
                    })
                    ->sum('jumlah');
            } else {
                // Untuk item FACILITY: cari transaksi yang tujuannya adalah facility ini
                // DAN kode materialnya cocok.
                $penerimaan = ItemTransaction::where('facility_to', $this->facility_id)
                    ->whereHas('item', function ($query) {
                        $query->where('kode_material', $this->kode_material);
                    })
                    ->sum('jumlah');
            }
            // =================================================================
        }

        return $this->stok_awal + $penerimaan - $penyaluran;
    }
}
