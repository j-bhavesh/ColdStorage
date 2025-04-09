<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @OA\Schema(
 *     schema="Company",
 *     title="Company",
 *     description="Company model",
 *     @OA\Property(property="id", type="integer", format="int64", description="The unique identifier of the company"),
 *     @OA\Property(property="name", type="string", description="The name of the company"),
 *     @OA\Property(property="contact_person", type="string", description="The contact person of the company"),
 *     @OA\Property(property="contact_number", type="string", description="The contact number of the company"),
 *     @OA\Property(property="address", type="string", description="The address of the company"),
 *     @OA\Property(property="created_at", type="string", format="date-time", description="The date and time when the company was created"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", description="The date and time when the company was last updated"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", nullable=true, description="The date and time when the company was soft deleted")
 * )
 */
class Company extends Model
{
    use HasFactory; 
    use SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'contact_number',
        'address',
        'created_by'
    ];

    /**
     * Get the seeds bookings for the company.
     */
    public function seedsBooking(): HasMany
    {
        return $this->hasMany(SeedsBooking::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the seed distributions for the company.
     */
    public function seedDistributions(): HasMany
    {
        return $this->hasMany(SeedDistribution::class);
    }
}