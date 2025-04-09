<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnloadingCompany extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_person',
        'contact_number',
        'address',
        'status',
        'created_by'
    ];

    /**
     * Get the storage unloadings for the unloading company.
     */
    public function storageUnloadings(): HasMany
    {
        return $this->hasMany(StorageUnloading::class, 'company_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
} 