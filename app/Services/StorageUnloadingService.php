<?php

namespace App\Services;

use App\Models\StorageUnloading;
use App\Models\UnloadingCompany;
use App\Models\ColdStorage;
use App\Models\Transporter;
use App\Models\Vehicle;
use App\Models\SeedVariety;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

use Barryvdh\DomPDF\Facade\Pdf;

class StorageUnloadingService
{
    /**
     * Get all storage unloadings with optional search and sorting
     */
    public function getAll(
        ?string $search = null,
        ?string $sortField = null,
        ?string $sortDirection = 'asc',
        ?int $perPage = 10,
        $financialYear = null
    ): LengthAwarePaginator 
    {
        $query = StorageUnloading::query()
            ->with(['unloadingCompany', 'coldStorage', 'transporter', 'vehicle', 'seedVariety', 'creator']);
        
        if( !empty( $financialYear ) ) {
            $query->when($financialYear, function ($q) use ($financialYear) {
                $startDate = \Carbon\Carbon::parse($financialYear['startDate'])->startOfDay();
                $endDate = \Carbon\Carbon::parse($financialYear['endDate'])->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('rst_no', 'like', "%{$search}%")
                    ->orWhere('chamber_no', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('vehicle_number', 'like', "%{$search}%")
                    ->when(is_numeric($search), function ($q) use ($search) {
                        $q->orWhere('bag_quantity', $search)
                        ->orWhereRaw("CAST(weight AS CHAR) LIKE ?", ["%{$search}%"]);
                    })
                    ->orWhereHas('unloadingCompany', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('coldStorage', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('transporter', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('seedVariety', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($sortField) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    /**
     * Find a storage unloading by ID
     */
    public function findById(int $id): ?StorageUnloading
    {
        return StorageUnloading::with(['unloadingCompany', 'coldStorage', 'transporter', 'vehicle', 'seedVariety', 'creator'])
            ->find($id);
    }

    /**
     * Create a new storage unloading
     */
    public function create(array $data): StorageUnloading
    {
        $data['vehicle_id'] = NULL;
        $data['created_by'] = auth()->user()->id;
        
        $storageUnLoading = StorageUnloading::create($data);
        $storageUnLoading->load(['unloadingCompany', 'coldStorage', 'transporter', 'vehicle', 'seedVariety', 'creator']);
        return $storageUnLoading;
    }

    /**
     * Update an existing storage unloading
     */
    public function update(int $id, array $data)
    {
        $storageUnloading = $this->findById($id);

        $data['vehicle_id'] = NULL;
        $data['created_by'] = auth()->user()->id;

        $storageUnloading->update($data);
        return $storageUnloading->fresh(['unloadingCompany', 'coldStorage', 'transporter', 'vehicle', 'seedVariety', 'creator']);
    }

    /**
     * Delete a storage unloading
     */
    public function delete(int $id): bool
    {
        try {
            $storageUnloading = $this->findById($id);
            \DB::transaction(function () use ($storageUnloading) {
                $storageUnloading->forceDelete();
            });
            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting storage unloading: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete storage unloading: ' . $e->getMessage());
        }
    }

    /**
     * Get active unloading companies
     */
    public function getActiveUnloadingCompanies(): Collection
    {
        return UnloadingCompany::where('status', 'active')
            ->orderBy('name')
            ->get();
    }

    /**
     * Get active cold storages
     */
    public function getActiveColdStorages(): Collection
    {
        return ColdStorage::orderBy('name')->get();
    }

    /**
     * Get active transporters
     */
    public function getActiveTransporters(): Collection
    {
        return Transporter::orderBy('name')->get();
    }

    /**
     * Get active vehicles
     */
    public function getActiveVehicles(): Collection
    {
        // return Vehicle::where('status', 'active')
        //     ->orderBy('vehicle_number')
        //     ->get();

        return Vehicle::orderBy('vehicle_number')->get();
    }

    /**
     * Get vehicles by transporter ID
     */
    public function getVehiclesByTransporter(int $transporterId): Collection
    {
        return Vehicle::where('transporter_id', $transporterId)
            ->orderBy('vehicle_number')
            ->get();
    }

    /**
     * Get active seed varieties
     */
    public function getActiveSeedVarieties(): Collection
    {
        // return SeedVariety::where('status', 'active')
        //     ->orderBy('name')
        //     ->get();
        return SeedVariety::orderBy('name')->get();
    }

    /**
     * Get total weight unloaded by company
     */
    public function getTotalWeightByCompany(int $companyId): float
    {
        return StorageUnloading::where('company_id', $companyId)
            ->sum('weight');
    }

    /**
     * Get total weight unloaded by cold storage
     */
    public function getTotalWeightByColdStorage(int $coldStorageId): float
    {
        return StorageUnloading::where('cold_storage_id', $coldStorageId)
            ->sum('weight');
    }

    /**
     * Get unloading statistics
     */
    public function getStatistics(): array
    {
        return [
            'total_unloadings' => StorageUnloading::count(),
            'active_companies' => UnloadingCompany::where('status', 'active')->count(),
            'active_cold_storages' => ColdStorage::count(),
            'active_transporters' => Transporter::count(),
            'active_vehicles' => Vehicle::count(),
            'active_seed_varieties' => SeedVariety::count(),
        ];
    }

    /**
     * Download perticuler storage uloading in pdf
     *
     * @return Collection
     */
    public function downloadStorageUnloadingPDF($id)
    {
        $sulPdf = $this->findById($id);
        // $sdPdf = SeedDistribution::with(['seedsBooking', 'farmer', 'farmer.user', 'seedVariety', 'company'])->find($id);
        
        $fileName = 'storage-unloading-' . $id . '.pdf';
        // $filePath = storage_path('app/public/challans/' . $fileName);

        // // Ensure directory exists
        // if (!file_exists(dirname($filePath))) {
        //     mkdir(dirname($filePath), 0777, true);
        // }

        // file_put_contents($filePath, $pdf);

        // load pdf view (resources/views/pdf/challan.blade.php)
        $pdf = Pdf::loadView('admin.storage-unloadings.storage-unloading-pdf', compact('sulPdf'));

        // $url = asset('storage/challans/' . $fileName);
        return $pdf->output();
    }
} 