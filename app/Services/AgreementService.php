<?php

namespace App\Services;

use App\Models\Agreement;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\SmsService;
use Illuminate\Support\Facades\DB;

class AgreementService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function getAll(
        ?string $search = null,
        ?string $sortField = null,
        ?string $sortDirection = null,
        int $perPage = 10,
        $financialYear = null
    ): LengthAwarePaginator
    {
        $query = Agreement::with(['farmer', 'farmer.user', 'seedVariety', 'creator']);

        if( !empty( $financialYear ) ) {
            $query->when($financialYear, function ($q) use ($financialYear) {
                $startDate = $financialYear['startDate'];
                $endDate = $financialYear['endDate'];

                $q->whereBetween('agreement_date', [$startDate, $endDate]);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('farmer', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('village_name', 'like', "%{$search}%")
                    ->orWhere('farmer_id', 'like', "%{$search}%");
                })
                
                // Farmer -> User relation
                ->orWhereHas('farmer.user', function ($q) use ($search) {
                    $q->where('phone', 'like', "%{$search}%");
                })
                
                // Search directly by farmer_id (on agreements table)
                ->orWhere('farmer_id', 'like', "%{$search}%")

                ->orWhereHas('seedVariety', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($sortField && in_array(strtolower($sortDirection ?? ''), ['asc', 'desc'])) {
            // $query->orderBy($sortField, $sortDirection);
            if ($sortField === 'farmer_name') {
                $query->join('farmers', 'agreements.farmer_id', '=', 'farmers.id')
                    ->select('agreements.*', 'farmers.name as farmer_name')
                    ->orderBy('farmers.name', $sortDirection);
            } elseif ($sortField === 'village_name') {
                $query->join('farmers', 'agreements.farmer_id', '=', 'farmers.id')
                    ->select('agreements.*', 'farmers.name as farmer_name', 'farmers.village_name')
                    ->orderBy('farmers.village_name', $sortDirection);
            } elseif ($sortField === 'phone') {
                $query->join('farmers', 'agreements.farmer_id', '=', 'farmers.id')
                    ->join('users', 'farmers.user_id', '=', 'users.id')
                    ->select('agreements.*', 'farmers.name as farmer_name', 'farmers.village_name', 'users.phone')
                    ->orderBy('users.phone', $sortDirection);
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    public function findById(int $id): ?Agreement
    {
        return Agreement::with(['farmer', 'farmerUser'])->find($id);
    }

    public function create(array $data): Agreement
    {
        $this->validateDuplicateAgreement($data);

        $data['created_by'] = auth()->user()->id;
        $agreement = Agreement::create($data);
        $agreement->load(['farmer', 'seedVariety', 'creator']);

        // Send SMS after successful agreement creation
        try {
            $phone = $agreement->farmer->user->phone ?? null;
            // $templateId = '1707174971330491233';
            $templateId = config('services.sms.potato_booking_template_id');
            $variables = [
                $agreement->farmer->name,
                $agreement->seedVariety->name,
                $agreement->agreement_date->format(env('DATE_FORMATE')),
                $agreement->rate_per_kg ?? '',
                $agreement->bag_quantity ?? ''
            ];
            if ($phone) {
                $this->smsService->sendTemplateSms($phone, $templateId, $variables);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send agreement SMS', [
                'error' => $e->getMessage(),
                'agreement_id' => $agreement->id ?? null
            ]);
        }

        return $agreement;
    }

    public function update(Agreement $agreement, array $data): Agreement
    {
        if (isset($data['farmer_id']) || isset($data['seed_variety_id']) || isset($data['agreement_date'])) {
            $this->validateDuplicateAgreement($data, $agreement);
        } 

        if (isset($data['bag_quantity'])) {
            $this->validateBagQuantityUpdate($agreement, $data['bag_quantity']);
            $this->updateBagCalculations($agreement, $data['bag_quantity']);
        }

        $data['created_by'] = auth()->user()->id;

        $agreement->update($data);

        // Send SMS after successful agreement update
        try {
            $phone = $agreement->farmer->user->phone ?? null;
            // $templateId = '1707174971330491233';
            $templateId = config('services.sms.potato_booking_template_id');
            $variables = [
                $agreement->farmer->name,
                $agreement->seedVariety->name,
                $agreement->agreement_date->format(env('DATE_FORMATE')),
                $agreement->rate_per_kg ?? '',
                $agreement->bag_quantity ?? ''
            ];
            if ($phone) {
                $this->smsService->sendTemplateSms($phone, $templateId, $variables);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send agreement SMS', [
                'error' => $e->getMessage(),
                'agreement_id' => $agreement->id ?? null
            ]);
        }

        return $agreement->fresh(['farmer', 'seedVariety', 'creator']);
    }

    // private function updateBagCalculations(Agreement $agreement, int $newBagQuantity): void
    // {
    //     // Get total distributed quantity for this booking
    //     $totalDistributed = $agreement->packagingDistributions()->sum('bag_quantity');

    //     // // Get total storage loaded bags (filled bags delivered by farmers)
    //     $totalStorageLoaded = $agreement->storageLoadings()->sum('bag_quantity');

    //     // Get total extra bags
    //     $totalExtraBagsQty = $agreement->storageLoadings()->sum('extra_bags');

    //     // Calculate surplus (if any)
    //     $surplusBags = max(0, $totalStorageLoaded - $newBagQuantity);
    //     // $surplusBags = 0;

    //     // // If there are extra bags, keep them as surplus unless they are now covered by new bag quantity
    //     // if ($totalLoaded > $newBagQuantity) {
    //     //     // There are still surplus bags
    //     //     $surplusBags = $totalLoaded - $newBagQuantity;
    //     // } elseif ($newBagQuantity > ($previousTotal + $agreement->surplus_bags)) {
    //     //     // If admin increases the total enough to absorb surplus, reset it
    //     //     $surplusBags = 0;
    //     // } else {
    //     //     // Keep existing surplus (for tracking)
    //     //     $surplusBags = $agreement->surplus_bags;
    //     // }

    //     $totalSurplsBags = max(0, $totalExtraBagsQty + $surplusBags);
        
    //     // Update the seeds booking record
    //     $agreement->received_bags = $totalStorageLoaded;
    //     $agreement->pending_bags = $newBagQuantity - $totalStorageLoaded;
    //     $agreement->surplus_bags = $surplusBags;
    //     $agreement->save();
        
    //     // Get all distributions ordered by date and ID to maintain chronological order
    //     $distributions = $agreement->packagingDistributions()
    //         ->orderBy('distribution_date', 'asc')
    //         ->orderBy('id', 'asc')
    //         ->get();
        
    //     $cumulativeDistributed = 0;
        
    //     // Update each distribution record with correct pending bags calculation
    //     foreach ($distributions as $distribution) {
    //         $cumulativeDistributed += $distribution->bag_quantity;
            
    //         $distribution->received_bags = $cumulativeDistributed;
    //         $distribution->pending_bags = $newBagQuantity - $cumulativeDistributed;
    //         $distribution->save();
    //     }


    //     $storageLoadings = $agreement->storageLoadings()
    //         ->orderBy('created_at', 'asc')
    //         ->orderBy('id', 'asc')
    //         ->get();
        
    //     $cumulativeLoaded = 0;
        
    //     // Update each storage loading record with correct pending bags calculation
    //     foreach ($storageLoadings as $loading) {
    //         $cumulativeLoaded += $loading->bag_quantity;

    //         // Calculate how much surplus exists up to this point
    //         $currentSurplus = max(0, $cumulativeLoaded - $newBagQuantity);
            
    //         $loading->received_bags = $cumulativeLoaded;
    //         $loading->pending_bags = $newBagQuantity - $cumulativeLoaded;
    //         $loading->extra_bags = $currentSurplus;
    //         $loading->save();
    //     }
    // }

    private function updateBagCalculations(Agreement $agreement, int $newBagQuantity): void
    {
        // Get total distributed quantity for this booking
        $totalDistributed = $agreement->packagingDistributions()->sum('bag_quantity');
     
        // Calculate ACTUAL total received (bag_quantity + extra_bags from all storage loadings)
        $storageLoadings = $agreement->storageLoadings()
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        
        // Calculate the actual total received including previous extra bags
        $actualTotalReceived = $storageLoadings->sum(function($loading) {
            return $loading->bag_quantity + $loading->extra_bags;
        });
        
        // Calculate surplus based on actual total received vs new agreement quantity
        $surplusBags = max(0, $actualTotalReceived - $newBagQuantity);
        
        // Update the agreement record
        $agreement->received_bags = $actualTotalReceived;
        $agreement->pending_bags = max(0, $newBagQuantity - $actualTotalReceived);
        $agreement->surplus_bags = $surplusBags;
        $agreement->save();
        
        // Get all distributions ordered by date and ID to maintain chronological order
        $distributions = $agreement->packagingDistributions()
            ->orderBy('distribution_date', 'asc')
            ->orderBy('id', 'asc')
            ->get();
        
        $cumulativeDistributed = 0;
        
        // Update each distribution record with correct pending bags calculation
        foreach ($distributions as $distribution) {
            $cumulativeDistributed += $distribution->bag_quantity;
            
            $distribution->received_bags = $cumulativeDistributed;
            $distribution->pending_bags = $newBagQuantity - $cumulativeDistributed;
            $distribution->save();
        }
     
        // Now recalculate storage loadings with the new agreement quantity
        $cumulativeReceived = 0;
        
        // Update each storage loading record
        foreach ($storageLoadings as $loading) {
            // Add the actual received bags from this loading (including previous extra bags)
            $thisLoadingTotal = $loading->bag_quantity + $loading->extra_bags;
            $cumulativeReceived += $thisLoadingTotal;
     
            // Calculate how much surplus exists up to this point
            $currentSurplus = max(0, $cumulativeReceived - $newBagQuantity);
            
            // If there's surplus, the extra bags are the difference
            // Otherwise, all bags are covered by the agreement
            if ($currentSurplus > 0) {
                // There are extra bags beyond the agreement quantity
                $loading->bag_quantity = $thisLoadingTotal - $currentSurplus;
                $loading->extra_bags = $currentSurplus;
            } else {
                // All bags are covered by the agreement
                $loading->bag_quantity = $thisLoadingTotal;
                $loading->extra_bags = 0;
            }
            
            $loading->received_bags = $cumulativeReceived;
            $loading->pending_bags = max(0, $newBagQuantity - $cumulativeReceived);
            $loading->save();
        }
    }

    private function validateBagQuantityUpdate(Agreement $agreement, int $newBagQuantity): void
    {
        // Get total distributed quantity for this booking
        $totalDistributed = $agreement->packagingDistributions()->sum('bag_quantity');
        
        // Get total storage loading quantity (bags delivered by farmer)
        $totalStorageLoaded = $agreement->storageLoadings()->sum('bag_quantity');
        
        // Determine the minimum allowed quantity based on both distributions and storage loadings
        $minimumAllowed = max($totalDistributed, $totalStorageLoaded);
        
        // If there are distributions or storage loadings and new quantity is less than the minimum
        if ($minimumAllowed > 0 && $newBagQuantity < $minimumAllowed) {
            $reasons = [];
            if ($totalDistributed > 0) {
                $reasons[] = "{$totalDistributed} bags have already been distributed";
            }
            if ($totalStorageLoaded > 0) {
                $reasons[] = "{$totalStorageLoaded} bags have already been delivered by farmer";
            }
            
            $reasonText = implode(' and ', $reasons);
            throw new \Exception("Cannot reduce bag quantity to {$newBagQuantity} because {$reasonText}. Minimum allowed quantity: {$minimumAllowed}");
        }
        
        // Basic validation
        if ($newBagQuantity <= 0) {
            throw new \Exception("Bag quantity must be greater than 0");
        }
    }

    private function validateDuplicateAgreement(array $data, ?Agreement $currentAgreement = null): void
    {
        $query = Agreement::where('farmer_id', $data['farmer_id'])
            ->where('seed_variety_id', $data['seed_variety_id'])
            ->where('status', 'active');

        if ($currentAgreement) {
            $query->where('id', '!=', $currentAgreement->id);
        }

        if ($query->exists()) {
            throw new \Exception('An active agreement already exists for this farmer and seed variety.');
        }
    }

    public function delete(int $id): bool
    {
        try {
            $agreement = $this->findById($id);
            if (!$agreement) {
                return false;
            }

            \DB::transaction(function () use ($agreement) {
                // 1. Permanently delete all associated records first

                // Permanently delete packaging distributions for this agreement
                $agreement->packagingDistributions()->forceDelete();

                // Permanently delete storage loadings for this agreement
                $agreement->storageLoadings()->forceDelete();

                // 2. Permanently delete the agreement record
                $agreement->forceDelete();
            });

            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting agreement: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete agreement and associated records: ' . $e->getMessage());
        }
    }
}
