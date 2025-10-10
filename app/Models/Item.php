<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_id';
    protected $fillable = [
        'nama_material',
        'kode_material',
        'kategori_material',
    ];

    public function initialStocks()
    {
        return $this->hasMany(InitialStock::class, 'item_id');
    }

    public function currentStocks()
    {
        return $this->hasMany(CurrentStock::class, 'item_id');
    }

    public function transactionLogs()
    {
        return $this->hasMany(TransactionLog::class, 'item_id');
    }

    public function destructionSubmissions()
    {
        return $this->hasMany(DestructionSubmission::class, 'item_id');
    }
}