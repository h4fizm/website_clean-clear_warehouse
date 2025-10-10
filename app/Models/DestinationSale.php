<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DestinationSale extends Model
{
    use HasFactory;

    protected $primaryKey = 'destination_id';
    protected $fillable = [
        'nama_tujuan',
        'keterangan',
    ];

    public function transactionLogs()
    {
        return $this->hasMany(TransactionLog::class, 'destination_sales_id');
    }
}