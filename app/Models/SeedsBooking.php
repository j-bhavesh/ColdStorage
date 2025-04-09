<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SeedsBooking extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'farmer_id',
        'company_id',
        'seed_variety_id',
        'booking_type',
        'booking_amount',
        'bag_quantity',
        'status',
        'pending_bags',
        'received_bags',
        'bag_rate',
        'created_by',
    ];

    // protected $casts = [
    //     'bag_rate' => 'decimal:2',
    // ];

    public function farmer()
    {
        return $this->belongsTo(Farmer::class)->withTrashed();
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

    public function seedVariety()
    {
        return $this->belongsTo(SeedVariety::class)->withTrashed();
    }

    public function company()
    {
        return $this->belongsTo(Company::class)->withTrashed();
    }

    /**
     * Get the seed distributions for the seeds booking.
     */
    public function seedDistributions(): HasMany
    {
        return $this->hasMany(SeedDistribution::class);
    }
}
