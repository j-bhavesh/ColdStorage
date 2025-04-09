<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="SeedVariety",
 *     title="Seed Variety",
 *     description="Seed variety model",
 *     @OA\Property(property="id", type="integer", format="int64", description="The unique identifier of the seed variety"),
 *     @OA\Property(property="name", type="string", description="The name of the seed variety"),
 *     @OA\Property(property="description", type="string", description="Description of the seed variety"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="The creation timestamp"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="The last update timestamp"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, description="The soft delete timestamp")
 * )
 */
class SeedVariety extends Model 
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'created_by'
    ];

    /**
     * Get the agreements for the seed variety.
     */
    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the seeds bookings for the seed variety.
     */
    public function seedsBooking(): HasMany
    {
        return $this->hasMany(SeedsBooking::class);
    }

    /**
     * Get the seed distributions for the seed variety.
     */
    public function seedDistributions(): HasMany
    {
        return $this->hasMany(SeedDistribution::class);
    }

    /**
     * Get the storage unloadings for the seed variety.
     */
    public function storageUnloadings(): HasMany
    {
        return $this->hasMany(StorageUnloading::class);
    }
}
