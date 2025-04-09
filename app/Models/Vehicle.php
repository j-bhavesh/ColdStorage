<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_number',
        'transporter_id',
        'vehicle_type',
    ];

    /**
     * Get the transporter that owns the vehicle.
     */
    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

    /**
     * Get the challans for the vehicle.
     */
    public function challans(): HasMany
    {
        return $this->hasMany(Challan::class);
    }

    /**
     * Get the storage unloadings for the vehicle.
     */
    public function storageUnloadings(): HasMany
    {
        return $this->hasMany(StorageUnloading::class);
    }

    /**
     * Get the storage loadings for the vehicle.
     */
    public function storageLoadings(): HasMany
    {
        return $this->hasMany(StorageLoading::class);
    }
} 