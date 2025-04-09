<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorageUnloading extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'cold_storage_id',
        'transporter_id',
        'vehicle_id',
        'vehicle_number',
        'seed_variety_id',
        'rst_no',
        'chamber_no',
        'location',
        'bag_quantity',
        'weight',
        'created_by'
    ];

    protected $casts = [
        'bag_quantity' => 'integer',
        'weight' => 'decimal:2'
    ];

    /**
     * Get the company that owns the storage unloading.
     */
    public function unloadingCompany(): BelongsTo
    {
        return $this->belongsTo(UnloadingCompany::class, 'company_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the cold storage that owns the storage unloading.
     */
    public function coldStorage(): BelongsTo
    {
        return $this->belongsTo(ColdStorage::class);
    }

    /**
     * Get the transporter that owns the storage unloading.
     */
    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

    /**
     * Get the vehicle that owns the storage unloading.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the seed variety that owns the storage unloading.
     */
    public function seedVariety(): BelongsTo
    {
        return $this->belongsTo(SeedVariety::class);
    }
} 