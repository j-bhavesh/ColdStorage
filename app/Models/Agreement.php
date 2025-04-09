<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Agreement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'farmer_id',
        'seed_variety_id',
        'rate_per_kg',
        'agreement_date',
        'vighas',
        'bag_quantity',
        'received_bags',
        'pending_bags',
        'surplus_bags',
        'created_by',
    ];

    protected $casts = [
        'agreement_date' => 'date',
        'rate_per_kg' => 'decimal:2',
        'vighas' => 'decimal:2',
        'bag_quantity' => 'integer',
        'received_bags' => 'integer',
        'pending_bags' => 'integer',
        'surplus_bags' => 'integer',
    ];

    /**
     * Get the farmer that owns the agreement.
     */
    public function farmer(): BelongsTo
    {
        return $this->belongsTo(Farmer::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the farmer user data
     */
    public function farmerUser()
    {
        return $this->hasOneThrough(User::class, Farmer::class, 'id', 'id', 'farmer_id', 'user_id');
    }

    /**
     * Get the seed variety that owns the agreement.
     */
    public function seedVariety(): BelongsTo
    {
        return $this->belongsTo(SeedVariety::class);
    }

    /**
     * Get the packaging distributions for the agreement.
     */
    public function packagingDistributions(): HasMany
    {
        return $this->hasMany(PackagingDistribution::class);
    }

    /**
     * Get the storage loadings for the agreement.
     */
    public function storageLoadings(): HasMany
    {
        return $this->hasMany(StorageLoading::class);
    }
}