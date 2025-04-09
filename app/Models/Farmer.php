<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @OA\Schema(
 *     schema="Farmer",
 *     title="Farmer",
 *     description="Farmer model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="contact_number", type="string", example="+1234567890"),
 *     @OA\Property(property="village_name", type="string", example="123 Main St"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class Farmer extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'village_name',
        'farmer_id',
        'user_id',
        'created_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function seedsBooking()
    {
        return $this->hasMany(SeedsBooking::class);
    }

    /**
     * Get the agreements for the farmer.
     */
    public function agreements(): HasMany
    {
        return $this->hasMany(Agreement::class);
    }

    /**
     * Get the advance payments for the farmer.
     */
    public function advancePayments(): HasMany
    {
        return $this->hasMany(AdvancePayment::class);
    }

    /**
     * Get the challans for the farmer.
     */
    public function challans(): HasMany
    {
        return $this->hasMany(Challan::class);
    }

    /**
     * Get the seed distributions for the farmer.
     */
    public function seedDistributions(): HasMany
    {
        return $this->hasMany(SeedDistribution::class);
    }
}