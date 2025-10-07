<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_transaction_id',
        'tujuan_sales',
    ];

    public function baseTransaction()
    {
        return $this->belongsTo(ItemTransaction::class, 'base_transaction_id');
    }
}