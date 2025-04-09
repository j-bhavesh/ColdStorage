<?php

namespace App\Services;

use App\Models\Agreement;
use App\Models\Farmer;
use App\Models\SeedVariety;
use App\Models\StorageLoading;
use App\Models\Vehicle;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\SmsService;
use Barryvdh\DomPDF\Facade\Pdf;

class StorageLoadingService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Get all storage loadings with pagination and search
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
        int $perPage = 10,
        $financialYear = null
    ): LengthAwarePaginator
    {
        $query = StorageLoading::query()
            ->with(['agreement.farmer', 'agreement.seedVariety', 'transporter', 'vehicle', 'coldStorage', 'agreement.farmerUser', 'creator']);
            if( !empty( $financialYear ) ) {
                $query->when($financialYear, function ($q) use ($financialYear) {
                    $startDate = $financialYear['startDate'];
                    $endDate = $financialYear['endDate'];

                    $q->whereBetween('created_at', [$startDate, $endDate]);
                });
            }

            $query->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('rst_number', 'like', '%' . $search . '%')
                        ->orWhere('vehicle_number', 'like', "%{$search}%")
                        ->orWhereHas('agreement.farmer', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%')
                                ->orWhere('farmer_id', 'like', '%' . $search . '%')
                                ->orWhere('village_name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('agreement.seedVariety', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('agreement.farmerUser', function ($q) use ($search) {
                            $q->where('phone', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('transporter', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('coldStorage', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
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
     * Get a single storage loading by ID
     *
     * @param int $id
     * @return StorageLoading
     */
    public function findById(int $id): StorageLoading
    {
        return StorageLoading::with(['agreement.farmer', 'agreement.seedVariety', 'transporter', 'vehicle', 'coldStorage', 'agreement.farmerUser', 'creator'])->findOrFail($id);
    }

    /**
     * Check if a storage loading is duplicate
     *
     * @param array $data
     * @param int|null $excludeId
     * @return bool
     */
    private function isDuplicate(array $data, ?int $excludeId = null): bool
    {
        $query = StorageLoading::where('agreement_id', $data['agreement_id'])
            ->where('transporter_id', $data['transporter_id'])
            ->where('vehicle_id', $data['vehicle_id'])
            ->where('cold_storage_id', $data['cold_storage_id'])
            ->where('rst_number', $data['rst_number']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Create a new storage loading
     *
     * @param array $data
     * @return StorageLoading
     * @throws \Exception
     */
    public function create(array $data)
    {
        if ($this->isDuplicate($data)) {
            throw new \Exception('Duplicate storage loading detected. Same RST number already exists for this combination of agreement, transporter, vehicle, and cold storage.');
        }

        // Mark agreement as completed immediately
        $agreement = Agreement::findOrFail($data['agreement_id']);

        // Check for existing storage loading records for this agreement
        // $existingLoading = StorageLoading::where('agreement_id', $data['agreement_id'])->first();

        //Get the total bags from the agreements tbl
        $totalBags = $agreement->bag_quantity;

        // Get the bags from the loading form
        $loadingBags = $data['bag_quantity'];

        // Calculate the received, pending and surplus bags
        if (!empty($agreement)) {
            // If there's existing loading, add current bags to previous received bags
            $totalReceivedBags = $agreement->received_bags + $loadingBags;
            $pendingBags = $totalBags - $totalReceivedBags;
        } else {
            // First time loading for this agreement
            $totalReceivedBags = $loadingBags;
            $pendingBags = $totalBags - $loadingBags;
        }

        // Calculate the received, pending and surplus bags
        // $totalReceivedBags = $loadingBags;
        // $pendingBags = $totalBags - $loadingBags;
        $surplusgBags = isset($data['extra_bags']) && !empty( $data['extra_bags'] ) ? $data['extra_bags'] : 0;

        // Set the pending_bags data
        $data['pending_bags'] = $pendingBags;

        // Validate the bag qty
        $this->validateBagQuantity($data['agreement_id'], $data['bag_quantity']);

        $data['vehicle_id'] = NULL;

        $data['created_by'] = auth()->user()->id;

        // Store the data into the storage_loading tbl
        $storageLoading = StorageLoading::create($data);
        $storageLoading->load(['agreement.farmer', 'agreement.seedVariety', 'coldStorage', 'vehicle', 'transporter', 'creator']);

        // Save the fields into the agreements tbl
        $agreement->received_bags = $totalReceivedBags;
        $agreement->pending_bags = $pendingBags;
        $agreement->surplus_bags = $surplusgBags;
        $agreement->status = 'completed';
        $agreement->save();

        // Send SMS after successful storage loading
        try {
            $phone = $storageLoading->agreement->farmer->user->phone ?? null;
            // $templateId = '1707174973441278926';
            $templateId = config('services.sms.storage_loading_template_id');
            $variables = [
                $storageLoading->agreement->farmer->name,
                $storageLoading->coldStorage->name,
                $storageLoading->rst_number,
                $storageLoading->created_at->format(env('DATE_FORMATE')),
                $storageLoading->chamber_no,
                $storageLoading->vehicle_number,
                $storageLoading->net_weight,
                $storageLoading->bag_quantity,
                // $storageLoading->extra_bags ?? 0
            ];
            if ($phone) {
                $this->smsService->sendTemplateSms($phone, $templateId, $variables);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send storage loading SMS', [
                'error' => $e->getMessage(),
                'loading_id' => $storageLoading->id ?? null
            ]);
        }

        return $storageLoading;
    }

    /**
     * Update an existing storage loading
     *
     * @param int $id
     * @param array $data
     * @return StorageLoading
     * @throws \Exception
     */
    public function update(int $id, array $data)
    {
        // Validate the bag qty
        $this->validateBagQuantity($data['agreement_id'], $data['bag_quantity'], $id);
        
        // Mark agreement as completed immediately
        $agreement = Agreement::find($data['agreement_id']);

        //Get the total bags from the agreements tbl
        $totalBags = $agreement->bag_quantity;

        // Get the bags from the loading form
        $loadingBags = $data['bag_quantity'];

        // Get the current storage loading record being updated
        $currentStorageLoading = StorageLoading::find($id); // Replace with your actual model and ID
        $oldBagQuantity = $currentStorageLoading->bag_quantity; // Get the old bag quantity

        // Calculate the received, pending and surplus bags
        if (!empty($agreement)) {
            // For update: subtract old quantity and add new quantity
            $totalReceivedBags = $agreement->received_bags - $oldBagQuantity + $loadingBags;
            $pendingBags = $totalBags - $totalReceivedBags;
        } else {
            // First time loading for this agreement
            $totalReceivedBags = $loadingBags;
            $pendingBags = $totalBags - $loadingBags;
        }

        // Calculate the received, pending and surplus bags
        // $totalReceivedBags = $loadingBags;
        // $pendingBags = $totalBags - $loadingBags;
        $surplusgBags = isset($data['extra_bags']) && !empty( $data['extra_bags'] ) ? $data['extra_bags'] : 0;

        // Save the fields into the agreements tbl
        $agreement->received_bags = $totalReceivedBags;
        $agreement->pending_bags = $pendingBags;
        $agreement->surplus_bags = $surplusgBags;
        $agreement->status = 'completed';
        $agreement->save();

        // Set the pending_bags data
        $data['pending_bags'] = $pendingBags;

        // Get the data by id from the storage_loading tbl
        $storageLoading = $this->findById($id);

        $data['vehicle_id'] = NULL;

        $data['created_by'] = auth()->user()->id;

        // Update the data  into the storage_loading tbl
        $storageLoading->update($data);

        // Send SMS after successful storage loading
        try {
            $phone = $storageLoading->agreement->farmer->user->phone ?? null;
            // $templateId = '1707174973441278926';
            $templateId = config('services.sms.storage_loading_template_id');
            $variables = [
                $storageLoading->agreement->farmer->name,
                $storageLoading->coldStorage->name,
                $storageLoading->rst_number,
                $storageLoading->created_at->format(env('DATE_FORMATE')),
                $storageLoading->chamber_no,
                $storageLoading->vehicle_number,
                $storageLoading->net_weight,
                $storageLoading->bag_quantity,
                // $storageLoading->extra_bags ?? 0
            ];
            if ($phone) {
                $this->smsService->sendTemplateSms($phone, $templateId, $variables);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send storage loading SMS', [
                'error' => $e->getMessage(),
                'loading_id' => $storageLoading->id ?? null
            ]);
        }

        return $storageLoading->fresh(['agreement', 'transporter', 'vehicle', 'coldStorage', 'creator']);
    }

    /**
     * Delete a storage loading
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $storageLoading = $this->findById($id);
            $agreementId = $storageLoading->agreement_id;
            $seedVarietyId = $storageLoading->agreement->seed_variety_id;
            $farmer_id = $storageLoading->agreement->farmer_id;

            \DB::transaction(function () use ($storageLoading, $agreementId, $seedVarietyId, $farmer_id) {
                // 1. Permanently delete the storage loading record
                $storageLoading->forceDelete();

                // 2. Check if there are any remaining storage loadings for this agreement
                // $remainingLoadings = StorageLoading::where('agreement_id', $agreementId)->count();
                $remainingLoadings = StorageLoading::where('agreement_id', $agreementId)->get();
                $remainingCount = $remainingLoadings->count();

                $agreement = Agreement::findOrFail($agreementId);

                $totalAgreementBags = $agreement->bag_quantity;

                if($remainingCount == 0){

                    // // Check if there are any other active agreements with the same seed variety
                    // $otherActiveAgreements = Agreement::where('seed_variety_id', $seedVarietyId)
                    //     ->where('id', '!=', $agreementId)
                    //     ->where('farmer_id', $farmer_id)
                    //     ->where('status', 'active')
                    //     ->count();

                    // if ($otherActiveAgreements == 0){
                    //     $agreement->status = 'active';
                    //     $agreement->received_bags = 0;
                    //     $agreement->pending_bags = 0;
                    //     $agreement->surplus_bags = 0;
                    //     $agreement->save();
                    // }
                    // No remaining loadings - reset agreement to initial state
                    $agreement->status = 'active';
                    $agreement->received_bags = 0;
                    $agreement->pending_bags = $totalAgreementBags; // Set pending bags to total agreement bags
                    $agreement->surplus_bags = 0;
                    $agreement->save();
                } else {
                    // There are remaining loadings - recalculate based on remaining records
                    $totalRemainingBags = $remainingLoadings->sum('bag_quantity');
                    $newPendingBags = $totalAgreementBags - $totalRemainingBags;
                    
                    // Update agreement with recalculated values
                    $agreement->received_bags = $totalRemainingBags;
                    $agreement->pending_bags = $newPendingBags;
                    $agreement->save();
                }
            });

            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting storage loading: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete storage loading: ' . $e->getMessage());
        }
    }

    /**
     * Get storage loadings by agreement ID
     *
     * @param int $agreementId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getByAgreementId(int $agreementId)
    {
        return StorageLoading::with(['agreement', 'transporter', 'vehicle', 'coldStorage'])
            ->where('agreement_id', $agreementId)
            ->get();
    }

    /**
     * Get total bag quantity for a specific agreement
     *
     * @param int $agreementId
     * @param int|null $excludeStorageLoadingId
     * @return int
     */
    public function getTotalBagQuantityByAgreement(int $agreementId, ?int $excludeStorageLoadingId = null): int
    {
        $query = StorageLoading::where('agreement_id', $agreementId);

        if ($excludeStorageLoadingId) {
            $query->where('id', '!=', $excludeStorageLoadingId);
        }

        return $query->sum('bag_quantity');
    }

    /**
     * Get total net weight for a specific agreement
     *
     * @param int $agreementId
     * @return float
     */
    public function getTotalNetWeightByAgreement(int $agreementId): float
    {
        return StorageLoading::where('agreement_id', $agreementId)
            ->sum('net_weight');
    }

    public function getFarmersWithActiveAgreements()
    {
        // return Farmer::whereIn('id', function($query) {
        //     $query->select('farmer_id')
        //         ->from('agreements')
        //         ->where('status', 'active')
        //         ->distinct();
        // })->orderBy('name')->get();

        return Farmer::whereIn('id', function($query) {
            $query->select('farmer_id')
                ->from('agreements')
                ->where(function($subQuery) {
                    $subQuery->whereNull('agreements.pending_bags')->orWhere('agreements.pending_bags', '>', 0);
                })
                // ->where('status', 'active')  // uncomment if you need status filter
                ->distinct();
        })->orderBy('name')->get();
    }

    public function getSeedVarietiesForFarmer($farmerId)
    {
        return Agreement::select(
            'agreements.id as agreement_id',
            'seed_varieties.id',
            'seed_varieties.name'
        )
        ->join('seed_varieties', 'agreements.seed_variety_id', '=', 'seed_varieties.id')
        ->where('agreements.farmer_id', $farmerId)
        // ->where('agreements.status', 'active')
        ->orderBy('seed_varieties.name')
        ->get();
    }

    public function getAgreementsForFarmerAndSeedVariety($farmerId, $seedVarietyId)
    {
        return Agreement::where('farmer_id', $farmerId)
            ->where('seed_variety_id', $seedVarietyId)
            ->where('status', 'active')
            ->get();
    }

    public function getAgreementForFarmerAndSeedVariety($farmerId, $seedVarietyId)
    {
        return Agreement::where('farmer_id', $farmerId)
            ->where('seed_variety_id', $seedVarietyId)
            // ->where('status', 'active')
            ->first();
    }

    public function validateBagQuantity($agreementId, $bagQuantity, $excludeStorageLoadingId = null)
    {
        $agreement = Agreement::findOrFail($agreementId);
        $totalLoadedBags = $this->getTotalBagQuantityByAgreement($agreementId, $excludeStorageLoadingId);

        if (($totalLoadedBags + $bagQuantity) > $agreement->bag_quantity) {
            throw new \Exception('Bag quantity exceeds the agreement limit. Remaining bags: ' . ($agreement->bag_quantity - $totalLoadedBags));
        }

        return true;
    }

    public function findAgreementById($id)
    {
        return Agreement::find($id);
    }

    public function getUsedBagsForAgreement($agreementId, $excludeStorageLoadingId = null)
    {

        $query = StorageLoading::where('agreement_id', $agreementId);

        if ($excludeStorageLoadingId) {
            $query->where('id', '!=', $excludeStorageLoadingId);
        }

        $usedBagsForAgreement = $query->sum('bag_quantity');
        return $usedBagsForAgreement;
        // return $query->sum('bag_quantity');
    }

    public function checkBagQuantity($agreementId, $newBagQuantity, $storageLoadingId = null)
    {
        $agreement = Agreement::findOrFail($agreementId);
        $loadedBags = $this->getUsedBagsForAgreement($agreementId,$storageLoadingId);
        

        // if( !empty( $agreement->pending_bags ) ) {
        //     $remainingBags = $agreement->pending_bags - $loadedBags;
        // } else {
            $remainingBags = $agreement->bag_quantity - $loadedBags;
        // }

        return [
            'is_exceeded' => $newBagQuantity > $remainingBags,
            'remaining_bags' => $remainingBags,
            'pending_bags' => $agreement->pending_bags,
            'surplus_bags' => $agreement->surplus_bags,
            'agreement_bags' => $agreement->bag_quantity,
            'loaded_bags' => $loadedBags,
            'extra_bags' => $newBagQuantity - $remainingBags
        ];
    }

    /**
     * Get vehicles by transporter ID
     */
    public function getVehiclesByTransporter(int $transporterId)
    {
        return Vehicle::where('transporter_id', $transporterId)
            ->orderBy('vehicle_number')
            ->get();
    }

    /**
     * Download perticuler seed distribution in pdf
     *
     * @return Collection
     */
    public function downloadStorageLoadingPDF($id)
    {
        $slPdf = $this->findById($id);
        // $sdPdf = SeedDistribution::with(['seedsBooking', 'farmer', 'farmer.user', 'seedVariety', 'company'])->find($id);
        
        $fileName = 'storage-loading-' . $id . '.pdf';
        // $filePath = storage_path('app/public/challans/' . $fileName);

        // // Ensure directory exists
        // if (!file_exists(dirname($filePath))) {
        //     mkdir(dirname($filePath), 0777, true);
        // }

        // file_put_contents($filePath, $pdf);

        // load pdf view (resources/views/pdf/challan.blade.php)
        $pdf = Pdf::loadView('admin.storage-loadings.storage-loading-pdf', compact('slPdf'));

        // $url = asset('storage/challans/' . $fileName);
        return $pdf->output();
    }
}
