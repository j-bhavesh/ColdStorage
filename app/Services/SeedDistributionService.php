<?php

namespace App\Services; 

use App\Models\SeedDistribution;
use App\Models\SeedsBooking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Services\SmsService;

use Barryvdh\DomPDF\Facade\Pdf;

class SeedDistributionService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Get all seed distributions with pagination
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 10, ?array $financialYear = null): LengthAwarePaginator
    {
        $query = SeedDistribution::query()
            ->with(['seedsBooking', 'farmer', 'farmer.user', 'seedVariety', 'company', 'creator']);

        if( !empty( $financialYear ) ) {
            $query->when($financialYear, function ($q) use ($financialYear) {
                $startDate = $financialYear['startDate'];
                $endDate = $financialYear['endDate'];

                $q->whereBetween('distribution_date', [$startDate, $endDate]);
            });
        }

        // Apply filters if any
        if (!empty($filters['seeds_booking_id'])) {
            $query->where('seeds_booking_id', $filters['seeds_booking_id']);
        }

        if (!empty($filters['farmer_id'])) {
            $query->where('farmer_id', $filters['farmer_id']);
        }

        if (!empty($filters['seed_variety_id'])) {
            $query->where('seed_variety_id', $filters['seed_variety_id']);
        }

        if (!empty($filters['company_id'])) {
            $query->where('company_id', $filters['company_id']);
        }

        if (!empty($filters['distribution_date'])) {
            $query->whereDate('distribution_date', $filters['distribution_date']);
        }

        // Apply search if provided
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('farmer', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                    ->orWhere('village_name', 'like', "%{$search}%")
                    ->orWhere('farmer_id', 'like', "%{$search}%");
                })
                ->orWhereHas('farmer.user', function($q) use ($search) {
                    $q->where('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('seedVariety', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('company', function($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhere('vehicle_number', 'like', "%{$search}%")
                ->orWhere('received_by', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        if (!empty($filters['sort_field'])) {
            $sortField = $filters['sort_field'];
            $sortDirection = $filters['sort_direction'] ?? 'asc';

            // Handle relationship sorting
            if (in_array($sortField, ['farmer_id', 'seed_variety_id', 'company_id'])) {
                $relation = match($sortField) {
                    'farmer_id' => 'farmer',
                    'seed_variety_id' => 'seedVariety',
                    'company_id' => 'company',
                    default => null
                };

                if ($relation) {
                    $tableName = match($relation) {
                        'farmer' => 'farmers',
                        'seedVariety' => 'seed_varieties',
                        'company' => 'companies',
                        default => null
                    };

                    if ($tableName) {
                        $query->join($tableName, 'seed_distributions.' . $sortField, '=', $tableName . '.id')
                              ->orderBy($tableName . '.name', $sortDirection)
                              ->select('seed_distributions.*');
                    }
                }
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    public function searchSeedDistributions($seedsDistributionsSearch)
    {
        $search = $seedsDistributionsSearch;

        $seedsDistrubutionsSearchResult = SeedsBooking::with(['farmer', 'company', 'seedVariety'])
            ->where(function ($query) use ($search) {
                if ($search) {
                    $query->whereHas('farmer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                        ->orWhere('farmer_id', 'like', "%{$search}%")
                        ->orWhere('village_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('company', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('seedVariety', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
                }
            })
            ->where(function ($query) {
                $query->where('pending_bags', '>', 0)
                      ->orWhereNull('pending_bags');
            })
            ->get()
            ->filter(function ($seedsBooking) {
                $totalDistributed = $seedsBooking->seedDistributions()->sum('bag_quantity');
                return $totalDistributed < $seedsBooking->bag_quantity;
            })
            ->map(function ($seedsBooking) {
                $pendingBags = $seedsBooking->pending_bags ?? $seedsBooking->bag_quantity;

                return [
                    'id' => $seedsBooking->id,
                    'text' => $seedsBooking->farmer->name . '-' .
                              $seedsBooking->company->name . '-' .
                              $seedsBooking->seedVariety->name . '-(#BID-' .
                              $seedsBooking->id . ') [Remaining: ' . $pendingBags . ']'
                ];
            });

        return $seedsDistrubutionsSearchResult;
    }

    /**
     * Get a single seed distribution by ID
     *
     * @param int $id
     * @return SeedDistribution|null
     */
    public function findById(int $id): ?SeedDistribution
    {
        return SeedDistribution::with(['seedsBooking', 'farmer', 'seedVariety', 'company'])
            ->find($id);
    }

    /**
     * Create a new seed distribution
     *
     * @param array $data
     * @return SeedDistribution
     * @throws \Exception
     */
    public function create(array $data): SeedDistribution
    {
        $this->validateBagQuantity($data['seeds_booking_id'], $data['bag_quantity']);
        
        // Get total bags from seeds_booking tbl
        $seedsBooking = SeedsBooking::findOrFail($data['seeds_booking_id']);

        // Get total bags
        $totalSeedsBags = $seedsBooking->bag_quantity;

        // Calculate the pending bags
        if( !empty( $seedsBooking->pending_bags ) || $seedsBooking->pending_bags != 0 ) {
            $pendingSeedsBags = $seedsBooking->pending_bags - $data['bag_quantity'];
            $receivedSeedsBags = $data['bag_quantity'] + $seedsBooking->received_bags;
        } else {
            $pendingSeedsBags = $totalSeedsBags - $data['bag_quantity'];
            $receivedSeedsBags = $data['bag_quantity'];
        }

        $seedsBooking->received_bags = (int)$receivedSeedsBags;
        $seedsBooking->pending_bags = $pendingSeedsBags;
        
        $seedsBooking->save();

        // Set pending and received bags for seed_distributions tble
        $data['pending_bags'] = $pendingSeedsBags;
        $data['received_bags'] = (int)$receivedSeedsBags;
        $data['created_by'] = auth()->user()->id;

        $seedDistribution = SeedDistribution::create($data);
        $seedDistribution->load(['farmer', 'company', 'seedVariety']);

        // Send SMS after successful distribution
        try {
            $phone = $seedDistribution->farmer->user->phone ?? null;
            // $templateId = '1707174973500101426';
            $templateId = config('services.sms.seed_distribution_template_id');
            $variables = [
                $seedDistribution->farmer->name,
                $seedDistribution->company->name,
                $seedDistribution->seedVariety->name,
                $seedDistribution->bag_quantity,
                $seedDistribution->distribution_date->format(env('DATE_FORMATE'))
            ];
            if ($phone) {
                $this->smsService->sendTemplateSms($phone, $templateId, $variables);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send seed distribution SMS', [
                'error' => $e->getMessage(),
                'distribution_id' => $seedDistribution->id ?? null
            ]);
        }

        return $seedDistribution;
    }

    /**
     * Update an existing seed distribution
     *
     * @param int $id
     * @param array $data
     * @return SeedDistribution|null
     * @throws \Exception
     */
    public function update(int $id, array $data): ?SeedDistribution
    {
        $seedDistribution = SeedDistribution::find($id);
        
        if ($seedDistribution) {
            // If bag quantity is being updated, validate it
            if (isset($data['bag_quantity'])) {
                $this->validateBagQuantity(
                    $data['seeds_booking_id'] ?? $seedDistribution->seeds_booking_id,
                    $data['bag_quantity'],
                    $id // Exclude current distribution from total calculation
                );
            }

            // Get total bags from seeds_booking tbl
            $seedsBooking = SeedsBooking::findOrFail($data['seeds_booking_id']);

            // Get total bags
            $totalSeedsBags = $seedsBooking->bag_quantity;
            $oldQuantity     = $seedDistribution->bag_quantity;
            $newQuantity     = $data['bag_quantity'] ?? $oldQuantity;

            // Recalculate received
            $totalReceivedBags = $seedsBooking->received_bags - $oldQuantity + $newQuantity;

            // // Calculate the pending bags
            // if( !empty( $seedsBooking->pending_bags ) || $seedsBooking->pending_bags != 0 ) {
            //     $totalReceivedBags = $seedsBooking->received_bags;
                
            //     $pendingSeedsBags = $seedsBooking->pending_bags - $data['bag_quantity'];
            //     $seedsTotalReceivedbags = (int)$data['bag_quantity'] + $totalReceivedBags;
            // } else{
            //     $pendingSeedsBags = $totalSeedsBags - $data['bag_quantity'];
            //     $seedsTotalReceivedbags = (int)$data['bag_quantity'];
            // }

            $pendingSeedsBags = $totalSeedsBags - $totalReceivedBags;
            
            $seedsBooking->pending_bags = $pendingSeedsBags;
            $seedsBooking->received_bags = $totalReceivedBags;
            
            $seedsBooking->save();

            // Set pending and received bags for seed_distributions tble
            $data['pending_bags'] = $pendingSeedsBags;
            // $data['received_bags'] = (int)$data['bag_quantity'];
            $data['received_bags'] = $totalReceivedBags;

            // Update the seeds distribution bag_qty while the pending bags available
            $data['bag_quantity'] = $newQuantity;
            
            $data['created_by'] = auth()->user()->id;
            
            $seedDistribution->update($data);

            // Send SMS after successful distribution
            try {
                $phone = $seedDistribution->farmer->user->phone ?? null;
                // $templateId = '1707174973500101426';
                $templateId = config('services.sms.seed_distribution_template_id');
                $variables = [
                    $seedDistribution->farmer->name,
                    $seedDistribution->company->name,
                    $seedDistribution->seedVariety->name,
                    $seedDistribution->bag_quantity,
                    $seedDistribution->distribution_date->format(env('DATE_FORMATE'))
                ];
                if ($phone) {
                    $this->smsService->sendTemplateSms($phone, $templateId, $variables);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send seed distribution SMS', [
                    'error' => $e->getMessage(),
                    'distribution_id' => $seedDistribution->id ?? null
                ]);
            }
            
            return $seedDistribution->fresh(['seedsBooking', 'farmer', 'seedVariety', 'company']);
        }

        return null;
    }

    private function validateBagQuantity(int $seedsBookingId, int $newBagQuantity, ?int $excludeDistributionId = null): void
    {
        $seedsBooking = SeedsBooking::findOrFail($seedsBookingId);

        // Get total distributed quantity excluding current distribution (for updates)
        $totalDistributed = SeedDistribution::where('seeds_booking_id', $seedsBookingId)
            ->when($excludeDistributionId, function ($query) use ($excludeDistributionId) {
                return $query->where('id', '!=', $excludeDistributionId);
            })
            ->sum('bag_quantity');

        // To check the excludeDistributionId is empty
        if( !empty( $excludeDistributionId ) ) {
            // Get the Seeds Distribution data
            $seedDistribution = SeedDistribution::findOrFail($excludeDistributionId);

            // Get the pending bags
            $pendingBags = $seedDistribution->pending_bags;
            $receivedBags = $seedDistribution->received_bags;

            if( !empty( $pendingBags ) || $pendingBags != 0 ) {
                // Calculate remaining quantity
                $remainingQuantity = $pendingBags;
                $totalDistributed = $receivedBags;
            } else {
                // Calculate remaining quantity
                $remainingQuantity = $seedsBooking->bag_quantity - $totalDistributed;
                $totalDistributed = $receivedBags;
            }
        } else {
            $remainingQuantity = $seedsBooking->bag_quantity - $totalDistributed;
        }


        if ($totalDistributed == $seedsBooking->bag_quantity) {
            throw new \Exception("All bags from this booking have been distributed. Cannot create new distribution.");
        }

        if ($newBagQuantity > $remainingQuantity) {
            throw new \Exception("Bag quantity cannot exceed remaining booking quantity. Remaining quantity: {$remainingQuantity}");
        }

        if ($newBagQuantity <= 0) {
            throw new \Exception("Bag quantity must be greater than 0");
        }
    }

    /**
     * Delete a seed distribution
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $seedDistribution = SeedDistribution::findOrFail($id);

            \DB::transaction(function () use ($seedDistribution) {

                // Get total bags from seeds_booking tbl
                $seedsBooking = SeedsBooking::findOrFail($seedDistribution->seeds_booking_id);

                $seedsBooking->received_bags = 0;
                $seedsBooking->pending_bags = 0;
                $seedsBooking->save();

                // Permanently delete the seed distribution record
                $seedDistribution->forceDelete();
            });

            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting seed distribution: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete seed distribution: ' . $e->getMessage());
        }
    }

    /**
     * Get seed distributions by agreement ID
     *
     * @param int $seedsBookingId
     * @return Collection
     */
    public function getByseedsBookingId(int $seedsBookingId): Collection
    {
        return SeedDistribution::with(['farmer', 'seedVariety', 'company'])
            ->where('seeds_booking_id', $seedsBookingId)
            ->get();
    }

    /**
     * Get seed distributions by farmer ID
     *
     * @param int $farmerId
     * @return Collection
     */
    public function getByFarmerId(int $farmerId): Collection
    {
        return SeedDistribution::with(['seedsBooking', 'seedVariety', 'company'])
            ->where('farmer_id', $farmerId)
            ->get();
    }

    /**
     * Get total bag quantity distributed for a specific seed variety
     *
     * @param int $seedVarietyId
     * @return int
     */
    public function getTotalBagQuantityBySeedVariety(int $seedVarietyId): int
    {
        return SeedDistribution::where('seed_variety_id', $seedVarietyId)
            ->sum('bag_quantity');
    }

    /**
     * Get total bag quantity distributed for a specific company
     *
     * @param int $companyId
     * @return int
     */
    public function getTotalBagQuantityByCompany(int $companyId): int
    {
        return SeedDistribution::where('company_id', $companyId)
            ->sum('bag_quantity');
    }

    /**
     * Download perticuler seed distribution in pdf
     *
     * @return Collection
     */
    public function downloadSeedDistributionPDF($id)
    {
        // $sdPdf = $this->findById($id);
        $sdPdf = SeedDistribution::with(['seedsBooking', 'farmer', 'farmer.user', 'seedVariety', 'company'])->find($id);
        
        $fileName = 'seed-distribution-' . $id . '.pdf';
        // $filePath = storage_path('app/public/challans/' . $fileName);

        // // Ensure directory exists
        // if (!file_exists(dirname($filePath))) {
        //     mkdir(dirname($filePath), 0777, true);
        // }

        // file_put_contents($filePath, $pdf);

        // load pdf view (resources/views/pdf/challan.blade.php)
        $pdf = Pdf::loadView('admin.seed-distributions.seed-distribution-pdf', compact('sdPdf'));

        // $url = asset('storage/challans/' . $fileName);
        return $pdf->output();
    }
}
