<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackagingDistribution extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agreement_id',
        'bag_quantity',
        'vehicle_number',
        'distribution_date',
        'received_by',
        'pending_bags',
        'received_bags',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'distribution_date' => 'date',
        'bag_quantity' => 'integer',
        'pending_bags' => 'integer',
        'received_bags' => 'integer',
    ];

    /**
     * Get the farmer that owns the packaging distribution.
     */
    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
