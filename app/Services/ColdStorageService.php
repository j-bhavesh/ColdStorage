<?php

namespace App\Services;

use App\Models\ColdStorage;
use Illuminate\Pagination\LengthAwarePaginator;

class ColdStorageService
{
    public function getAll(string $search = '', string $sortField = 'id', string $sortDirection = 'desc', int $perPage = 10): LengthAwarePaginator
    {
        return ColdStorage::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%")
                        ->orWhere('capacity', 'like', "%{$search}%");
                });
            })
            ->with(['creator'])
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    public function findById(int $id): ?ColdStorage
    {
        return ColdStorage::find($id);
    }

    public function create(array $data): ColdStorage
    {
        $data['created_by'] = auth()->user()->id;
        return ColdStorage::create($data);
    }

    public function update(int $id, array $data): bool
    {
        $coldStorage = $this->findById($id);
        if (!$coldStorage) {
            return false;
        }
        $data['created_by'] = auth()->user()->id;
        return $coldStorage->update($data);
    }

    public function delete(int $id): bool
    {
        try {
            $coldStorage = $this->findById($id);
            if (!$coldStorage) {
                return false;
            }
            
            \DB::transaction(function () use ($coldStorage) {
                // 1. Permanently delete all associated records first
                
                // Permanently delete storage loadings
                $coldStorage->storageLoadings()->forceDelete();
                
                // Permanently delete storage unloadings
                $coldStorage->storageUnloadings()->forceDelete();
                
                // 2. Permanently delete the cold storage record
                $coldStorage->forceDelete();
            });
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting cold storage: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete cold storage and associated records: ' . $e->getMessage());
        }
    }
} 