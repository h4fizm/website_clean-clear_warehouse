<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Item extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_id';
    protected $fillable = [
        'nama_material',
        'kode_material',
        'kategori_material',
    ];

    // Relasi ke stok saat ini
    public function currentStocks()
    {
        return $this->hasMany(CurrentStock::class, 'item_id');
    }

    // Relasi ke transaction logs
    public function transactionLogs()
    {
        return $this->hasMany(TransactionLog::class, 'item_id');
    }

    // Scope untuk mendapatkan stok di lokasi tertentu
    public function currentStockAtLocation($locationId)
    {
        return $this->currentStocks()->where('lokasi_id', $locationId)->first();
    }

    // Scope untuk mendapatkan item dengan stok di lokasi tertentu
    public function scopeWithStockAtLocation(Builder $query, $locationId)
    {
        return $query->whereHas('currentStocks', function ($q) use ($locationId) {
            $q->where('lokasi_id', $locationId);
        });
    }

    // Method untuk mendapatkan stok akhir di lokasi tertentu
    public function getStockAtLocation($locationId)
    {
        $stock = $this->currentStockAtLocation($locationId);
        return $stock ? $stock->current_quantity : 0;
    }

    // Method untuk update stok di lokasi tertentu
    public function updateStockAtLocation($locationId, $newQuantity)
    {
        $stock = $this->currentStockAtLocation($locationId);

        if ($stock) {
            $stock->update(['current_quantity' => $newQuantity]);
        } else {
            $this->currentStocks()->create([
                'lokasi_id' => $locationId,
                'current_quantity' => $newQuantity
            ]);
        }
    }

    // Method untuk increment stok
    public function incrementStockAtLocation($locationId, $quantity)
    {
        $currentStock = $this->getStockAtLocation($locationId);
        $newQuantity = $currentStock + $quantity;
        $this->updateStockAtLocation($locationId, $newQuantity);
        return $newQuantity;
    }

    // Method untuk decrement stok
    public function decrementStockAtLocation($locationId, $quantity)
    {
        $currentStock = $this->getStockAtLocation($locationId);

        if ($currentStock < $quantity) {
            throw new \Exception('Stok tidak mencukupi');
        }

        $newQuantity = $currentStock - $quantity;
        $this->updateStockAtLocation($locationId, $newQuantity);
        return $newQuantity;
    }

    // Relasi untuk mendapatkan semua transaksi terkait item ini
    public function getAllTransactions()
    {
        return $this->transactionLogs()
            ->with(['item', 'destinationSale'])
            ->orderBy('tanggal_transaksi', 'desc');
    }

    // Method untuk mencatat transaksi
    public function createTransaction($data)
    {
        return $this->transactionLogs()->create($data);
    }
}