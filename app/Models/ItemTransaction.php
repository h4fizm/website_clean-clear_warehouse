<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// This model now works with the new optimized schema as the primary
class ItemTransaction extends Model
{
    use HasFactory;

    public $timestamps = true;

    // Map the attributes to the new optimized schema
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
        'stok_awal_tujuan',
        'stok_akhir_tujuan',
        'jenis_transaksi',
        'no_surat_persetujuan',
        'no_ba_serah_terima',
        'keterangan_transaksi',
        'tahapan',
        'status',
    ];

    // Use the optimized table as primary
    protected $table = 'base_transactions';

    /*
    |--------------------------------------------------------------------------  
    | RELATIONSHIPS (Updated for new schema)
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
    
    // Relationships to specialized transaction types for access to specific data
    public function transferTransaction()
    {
        return $this->hasOne(TransferTransaction::class, 'base_transaction_id');
    }

    public function salesTransaction()
    {
        return $this->hasOne(SalesTransaction::class, 'base_transaction_id');
        // Access tujuan_sales through this relationship
    }

    public function pemusnahanTransaction()
    {
        return $this->hasOne(PemusnahanTransaction::class, 'base_transaction_id');
        // Access tanggal_pemusnahan, aktivitas_pemusnahan, penanggungjawab through this
    }

    public function penerimaanTransaction()
    {
        return $this->hasOne(PenerimaanTransaction::class, 'base_transaction_id');
    }

    public function penyaluranTransaction()
    {
        return $this->hasOne(PenyaluranTransaction::class, 'base_transaction_id');
    }
    
    // Accessor methods to get specialized data from related tables
    public function getTujuanSalesAttribute()
    {
        if ($this->salesTransaction) {
            return $this->salesTransaction->tujuan_sales;
        }
        return null;
    }
    
    public function getTanggalPemusnahanAttribute()
    {
        if ($this->pemusnahanTransaction) {
            return $this->pemusnahanTransaction->tanggal_pemusnahan;
        }
        return null;
    }
    
    public function getAktivitasPemusnahanAttribute()
    {
        if ($this->pemusnahanTransaction) {
            return $this->pemusnahanTransaction->aktivitas_pemusnahan;
        }
        return null;
    }
    
    public function getPenanggungjawabAttribute()
    {
        if ($this->pemusnahanTransaction) {
            return $this->pemusnahanTransaction->penanggungjawab;
        }
        return null;
    }
}