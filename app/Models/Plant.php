<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plant extends Model
{
    use HasFactory;

    protected $primaryKey = 'plant_id';
    protected $fillable = [
        'region_id',
        'nama_plant',
        'kode_plant',
        'kategori_plant',
        'provinsi',
        'kabupaten',
    ];

    public function region()
    {
        return $this->belongsTo(Region::class, 'region_id');
    }

    public function currentStocks()
    {
        return $this->hasMany(CurrentStock::class, 'lokasi_id');
    }

    public function transactionLogsAsActor()
    {
        return $this->hasMany(TransactionLog::class, 'lokasi_actor_id');
    }

    public function transactionLogsAsTarget()
    {
        return $this->hasMany(TransactionLog::class, 'lokasi_tujuan_id');
    }
}