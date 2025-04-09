<?php

namespace App\Services;

use App\Models\Vehicle;

class VehicleService
{
    public function getAll($search = '', $sortField = 'id', $sortDirection = 'desc', $perPage = 10)
    {
        return Vehicle::with(['transporter'])
            ->when($search, function ($query) use ($search) {
                $query->where('vehicle_number', 'like', '%' . $search . '%')
                    ->orWhere('vehicle_type', 'like', '%' . $search . '%')
                    ->orWhereHas('transporter', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%');
                    });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    public function create(array $data)
    {
        return Vehicle::create($data);
    }

    public function update($id, array $data)
    {
        $vehicle = $this->findById($id);
        $vehicle->update($data);
        return $vehicle;
    }

    public function delete($id)
    {
        try {
            $vehicle = $this->findById($id);
            \DB::transaction(function () use ($vehicle) {
                // 1. Permanently delete all associated records first
                
                // Permanently delete challans
                $vehicle->challans()->forceDelete();
                
                // Permanently delete storage loadings
                $vehicle->storageLoadings()->forceDelete();
                
                // Permanently delete storage unloadings
                $vehicle->storageUnloadings()->forceDelete();
                
                // 2. Permanently delete the vehicle record
                $vehicle->forceDelete();
            });
            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting vehicle: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete vehicle and associated records: ' . $e->getMessage());
        }
    }

    public function findById($id)
    {
        return Vehicle::with(['transporter'])->findOrFail($id);
    }
} 