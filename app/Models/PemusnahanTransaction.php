<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemusnahanTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'base_transaction_id',
        'tanggal_pemusnahan',
        'aktivitas_pemusnahan',
        'penanggungjawab',
    ];

    protected $casts = [
        'tanggal_pemusnahan' => 'date',
    ];

    public function baseTransaction()
    {
        return $this->belongsTo(ItemTransaction::class, 'base_transaction_id');
    }
}