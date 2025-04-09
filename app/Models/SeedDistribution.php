<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SeedDistribution extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'seeds_booking_id',
        'farmer_id',
        'seed_variety_id',
        'company_id',
        'bag_quantity',
        'distribution_date',
        'vehicle_number',
        'received_by',
        'received_bags',
        'pending_bags',
        'created_by',
    ];

    protected $casts = [
        'distribution_date' => 'date',
        'bag_quantity' => 'integer',
        'received_bags' => 'integer',
        'pending_bags' => 'integer',
    ];

    public function seedsBooking()
    {
        return $this->belongsTo(SeedsBooking::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function farmer()
    {
        return $this->belongsTo(Farmer::class);
    }

    public function seedVariety()
    {
        return $this->belongsTo(SeedVariety::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
