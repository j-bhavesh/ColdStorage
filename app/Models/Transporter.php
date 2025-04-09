<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transporter extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'contact_number',
        'created_by',
    ];

    /**
     * Get the vehicles for the transporter.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the storage unloadings for the transporter.
     */
    public function storageUnloadings(): HasMany
    {
        return $this->hasMany(StorageUnloading::class);
    }

    /**
     * Get the storage loadings for the transporter.
     */
    public function storageLoadings(): HasMany
    {
        return $this->hasMany(StorageLoading::class);
    }
} 