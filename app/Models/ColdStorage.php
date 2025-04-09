<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ColdStorage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'address',
        'capacity',
        'remarks',
        'created_by',
    ];

    /**
     * Get the storage loadings for the cold storage.
     */
    public function storageLoadings(): HasMany
    {
        return $this->hasMany(StorageLoading::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the storage unloadings for the cold storage.
     */
    public function storageUnloadings(): HasMany
    {
        return $this->hasMany(StorageUnloading::class);
    }
} 