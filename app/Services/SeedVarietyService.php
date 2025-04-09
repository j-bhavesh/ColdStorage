<?php

namespace App\Services;

use App\Models\SeedVariety;

class SeedVarietyService
{
    private function duplicateCheck(string $name, ?int $excludeId = null): ?SeedVariety
    {
        $query = SeedVariety::where('name', $name);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->first();
    }

    public function getAllSeedVarieties()
    {
        return SeedVariety::with(['creator'])->all();
    }

    public function createSeedVariety(array $data)
    {
        $existingVariety = $this->duplicateCheck($data['name']);
        
        if ($existingVariety) {
            return [
                'is_new' => false,
                'data' => $existingVariety
            ];
        }

        $data['created_by'] = auth()->user()->id;
        $seedVariety = SeedVariety::create($data);
        return [
            'is_new' => true,
            'data' => $seedVariety
        ];
    }

    public function getSeedVarietyById($id)
    {
        return SeedVariety::findOrFail($id);
    }

    public function updateSeedVariety(SeedVariety $seedVariety, array $data) : SeedVariety
    {
        $existingVariety = $this->duplicateCheck($data['name'], $seedVariety->id);
        
        if ($existingVariety) {
            throw new \Exception('A seed variety with this name already exists.');
        }

        $data['created_by'] = auth()->user()->id;
        $seedVariety->update($data);
        
        $seedVariety->refresh();

        return $seedVariety;
    }

    public function deleteSeedVariety(SeedVariety $seedVariety): bool
    {
        try {
            \DB::transaction(function () use ($seedVariety) {
                // 1. Permanently delete all associated records first
                
                // Permanently delete storage unloadings
                $seedVariety->storageUnloadings()->forceDelete();
                
                // Permanently delete seed distributions
                $seedVariety->seedDistributions()->forceDelete();
                
                // Permanently delete seeds bookings
                $seedVariety->seedsBooking()->forceDelete();
                
                // Handle agreements and their related records explicitly
                $agreements = $seedVariety->agreements;
                foreach ($agreements as $agreement) {
                    // Permanently delete packaging distributions for this agreement
                    $agreement->packagingDistributions()->forceDelete();
                    
                    // Permanently delete storage loadings for this agreement
                    $agreement->storageLoadings()->forceDelete();
                    
                    // Permanently delete the agreement
                    $agreement->forceDelete();
                }
                
                // 2. Permanently delete the seed variety record
                $seedVariety->forceDelete();
            });
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting seed variety: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete seed variety and associated records: ' . $e->getMessage());
        }
    }
}
