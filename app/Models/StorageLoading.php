<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StorageLoading extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agreement_id',
        'transporter_id',
        'vehicle_id',
        'vehicle_number',
        'cold_storage_id',
        'rst_number',
        'chamber_no',
        'bag_quantity',
        'net_weight',
        'extra_bags',
        'remarks',
        'received_bags',
        'pending_bags',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'bag_quantity' => 'integer',
        'net_weight' => 'decimal:2',
        'extra_bags' => 'integer',
        'received_bags' => 'integer',
        'pending_bags' => 'integer',
    ];

    /**
     * Get the agreement that owns the storage loading.
     */
    public function agreement(): BelongsTo
    {
        return $this->belongsTo(Agreement::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the transporter that owns the storage loading.
     */
    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

    /**
     * Get the vehicle that owns the storage loading.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the cold storage that owns the storage loading.
     */
    public function coldStorage(): BelongsTo
    {
        return $this->belongsTo(ColdStorage::class);
    }
} 