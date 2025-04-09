<?php

namespace App\Services;

use App\Models\UnloadingCompany;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UnloadingCompanyService
{
    /**
     * Get all unloading companies with pagination
     *
     * @param string $search
     * @param string $sortField
     * @param string $sortDirection
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(string $search = '', string $sortField = 'id', string $sortDirection = 'asc', int $perPage = 10): LengthAwarePaginator
    {
        return UnloadingCompany::query()
            ->with(['creator'])
            ->when($search, function ($query) use ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('contact_person', 'like', "%{$search}%")
                      ->orWhere('contact_number', 'like', "%{$search}%")
                      ->orWhere('id', 'like', "%{$search}%");
                });
            })
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }

    /**
     * Get unloading company by ID
     *
     * @param int $id
     * @return UnloadingCompany|null
     */
    public function getById(int $id): ?UnloadingCompany
    {
        return UnloadingCompany::findOrFail($id);
    }

    /**
     * Create new unloading company
     *
     * @param array $data
     * @return UnloadingCompany
     */
    public function create(array $data): UnloadingCompany
    {
        $data['created_by'] = auth()->user()->id;
        return UnloadingCompany::create($data);
    }

    /**
     * Update unloading company
     *
     * @param int $id
     * @param array $data
     * @return UnloadingCompany|null
     */
    public function update(int $id, array $data): ?UnloadingCompany
    {
        $unloadingCompany = $this->getById($id);
        if ($unloadingCompany) {
            $data['created_by'] = auth()->user()->id;
            $unloadingCompany->update($data);
            return $unloadingCompany;
        }
        return null;
    }

    /**
     * Delete unloading company
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $unloadingCompany = $this->getById($id);
            if (!$unloadingCompany) {
                return false;
            }
            
            \DB::transaction(function () use ($unloadingCompany) {
                // 1. Permanently delete all associated records first
                
                // Permanently delete storage unloadings
                $unloadingCompany->storageUnloadings()->forceDelete();
                
                // 2. Permanently delete the unloading company record
                $unloadingCompany->forceDelete();
            });
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting unloading company: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete unloading company and associated records: ' . $e->getMessage());
        }
    }

    /**
     * Get active unloading companies
     *
     * @return Collection
     */
    public function getActive(): Collection
    {
        return UnloadingCompany::where('status', 'active')->get();
    }
} 