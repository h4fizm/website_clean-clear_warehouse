<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_region',
    ];

    /**
     * Get all of the facilities for the Region
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}