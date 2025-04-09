<?php

namespace App\Services;

use App\Models\Transporter;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TransporterService
{
    /**
     * Get all transporters with pagination
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAllTransporters(int $perPage = 10): LengthAwarePaginator
    {
        return Transporter::with(['creator'])->latest()->paginate($perPage);
    }

    /**
     * Get all transporters without pagination
     *
     * @return Collection
     */
    public function getAllTransportersWithoutPagination(): Collection
    {
        return Transporter::with(['creator'])->latest()->get();
    }

    /**
     * Get a specific transporter by ID
     *
     * @param int $id
     * @return Transporter|null
     */
    public function getTransporterById(int $id): ?Transporter
    {
        return Transporter::find($id);
    }

    /**
     * Create a new transporter
     *
     * @param array $data
     * @return Transporter
     */
    public function createTransporter(array $data): Transporter
    {
        $data['created_by'] = auth()->user()->id;
        return Transporter::create($data);
    }

    /**
     * Update an existing transporter
     *
     * @param int $id
     * @param array $data
     * @return Transporter|null
     */
    public function updateTransporter(int $id, array $data): ?Transporter
    {
        $transporter = Transporter::find($id);
        if ($transporter) {
            $data['created_by'] = auth()->user()->id;
            $transporter->update($data);
        }
        return $transporter;
    }

    /**
     * Delete a transporter
     *
     * @param int $id
     * @return bool
     */
    public function deleteTransporter(int $id): bool
    {
        try {
            $transporter = Transporter::find($id);
            if (!$transporter) {
                return false;
            }
            
            \DB::transaction(function () use ($transporter) {
                // 1. Permanently delete all associated records first
                
                // Permanently delete storage loadings
                $transporter->storageLoadings()->forceDelete();
                
                // Permanently delete storage unloadings
                $transporter->storageUnloadings()->forceDelete();
                
                // Permanently delete vehicles
                $transporter->vehicles()->forceDelete();
                
                // 2. Permanently delete the transporter record
                $transporter->forceDelete();
            });
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting transporter: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete transporter and associated records: ' . $e->getMessage());
        }
    }
} 