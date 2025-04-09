<?php

namespace App\Services;

use App\Models\Challan;
use App\Models\Farmer;
use App\Models\Vehicle;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Barryvdh\DomPDF\Facade\Pdf;

class ChallanService
{
    /**
     * Get all challans with pagination and search
     *
     * @param string $search
     * @param string $sortField
     * @param string $sortDirection
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(
        ?string $search = null,
        ?string $sortField = null,
        ?string $sortDirection = null,
        int $perPage = 10): LengthAwarePaginator
    {
        $query = Challan::query()
            ->with(['farmer', 'vehicle', 'creator'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('challan_number', 'like', '%' . $search . '%')
                    ->orWhere('vehicle_number', 'like', '%' . $search . '%')
                    ->orWhereHas('farmer', function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                        ->orWhere('farmer_id', 'like', '%' . $search . '%');
                    })
                    ->orWhereHas('vehicle', function ($q) use ($search) {
                        $q->where('vehicle_number', 'like', '%' . $search . '%');
                    });
                });
            });

        // Only apply sorting if both field and direction are provided
        if ($sortField && $sortDirection) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            // Default sorting
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a single challan by ID
     *
     * @param int $id
     * @return Challan
     */
    public function findById(int $id): Challan
    {
        return Challan::with(['farmer', 'farmer.user', 'vehicle', 'creator'])->findOrFail($id);
    }

    /**
     * Check if a challan number is duplicate
     *
     * @param string $challanNumber
     * @param int|null $excludeId
     * @return bool
     */
    private function isDuplicate(string $challanNumber, ?int $excludeId = null): bool
    {
        $query = Challan::where('challan_number', $challanNumber);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Create a new challan
     *
     * @param array $data
     * @return Challan
     * @throws \Exception
     */
    public function create(array $data): Challan
    {
        if ($this->isDuplicate($data['challan_number'])) {
            throw new \Exception('Duplicate challan number detected. This challan number is already in use.');
        }
        $data['vehicle_id'] = NULL;
        $data['created_by'] = auth()->user()->id;

        $challan = Challan::create($data);
        $challan->load(['farmer', 'vehicle', 'creator']);
        
        return $challan;
        // return Challan::create($data);
    }

    /**
     * Update an existing challan
     *
     * @param int $id
     * @param array $data
     * @return Challan
     * @throws \Exception
     */
    public function update(int $id, array $data): Challan
    {
        $challan = $this->findById($id);
        
        if (isset($data['challan_number']) && $this->isDuplicate($data['challan_number'], $id)) {
            throw new \Exception('Duplicate challan number detected. This challan number is already in use.');
        }

        $data['vehicle_id'] = NULL;
        $data['created_by'] = auth()->user()->id;
        $challan->update($data);
        return $challan->fresh(['farmer', 'vehicle', 'creator']);
    }

    /**
     * Delete a challan
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $challan = $this->findById($id);
            \DB::transaction(function () use ($challan) {
                $challan->forceDelete();
            });
            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting challan: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete challan: ' . $e->getMessage());
        }
    }

    /**
     * Get active farmers
     *
     * @return Collection
     */
    public function getActiveFarmers(): Collection
    {
        return Farmer::orderBy('name')->get();
    }

    /**
     * Get active vehicles
     *
     * @return Collection
     */
    public function getActiveVehicles(): Collection
    {
        return Vehicle::orderBy('vehicle_number')->get();
    }

    /**
     * Download perticuler challan in pdf
     *
     * @return Collection
     */
    public function downloadChallanPDF($id)
    {
        $challans = $this->findById($id);
        
        $fileName = 'challan-' . $id . '.pdf';
        // $filePath = storage_path('app/public/challans/' . $fileName);

        // // Ensure directory exists
        // if (!file_exists(dirname($filePath))) {
        //     mkdir(dirname($filePath), 0777, true);
        // }

        // file_put_contents($filePath, $pdf);

        // load pdf view (resources/views/pdf/challan.blade.php)
        $pdf = Pdf::loadView('admin.challans.challan-pdf', compact('challans'));

        // $url = asset('storage/challans/' . $fileName);
        return $pdf->output();
    }
} 