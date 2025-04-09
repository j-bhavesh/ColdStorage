<?php

namespace App\Services; 

use App\Models\SeedsBooking;
use App\Models\Agreement;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\SmsService;

class SeedsBookingService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function getAll($search = '', $perPage = 10)
    {
        return SeedsBooking::with(['farmer', 'farmer.user', 'company', 'seedVariety', 'creator'])
            ->whereHas('farmer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('village_name', 'like', "%{$search}%")
                ->orWhere('farmer_id', 'like', "%{$search}%");
            })
            ->orWhereHas('farmer.user', function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%");
            })
            ->orWhereHas('company', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('seedVariety', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getById(int $id): SeedsBooking
    {
        return SeedsBooking::with(['farmer', 'company', 'seedVariety' ])->findOrFail($id);
    }

    public function create(array $data): SeedsBooking
    {
        $this->validateDuplicate($data);

        $data['booking_amount'] = $data['booking_amount'] ?? 0.00;
        $data['bag_rate'] = $data['bag_rate'] ?? 0.00;
        $data['created_by'] = auth()->user()->id;

        $SeedsBooking = SeedsBooking::create($data);
        $SeedsBooking->load(['farmer', 'company', 'seedVariety']);

        try {
            $phone = $SeedsBooking->farmer->user->phone ?? null;
            // $templateId = '1707174973476582552';
            $templateId = config('services.sms.seed_booking_template_id');
            $variables = [
                $SeedsBooking->farmer->name,
                $SeedsBooking->company->name,
                $SeedsBooking->seedVariety->name,
                $SeedsBooking->bag_quantity,
                $SeedsBooking->bag_rate,
                $SeedsBooking->booking_amount,
                $SeedsBooking->created_at->format(env('DATE_FORMATE'))
            ];
            if ($phone) {
                $this->smsService->sendTemplateSms($phone, $templateId, $variables);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send seed booking SMS', [
                'error' => $e->getMessage(),
                'booking_id' => $SeedsBooking->id ?? null
            ]);
        }

        return $SeedsBooking;
    }

    public function update(SeedsBooking $SeedsBooking, array $data): SeedsBooking
    {
        
        $this->validateDuplicate($data, $SeedsBooking->id);

        // Add validation to prevent reducing bag quantity below distributed amount
        if (isset($data['bag_quantity'])) {
            $this->validateBagQuantityUpdate($SeedsBooking, $data['bag_quantity']);
        }

        if( $data['booking_type'] == 'debit' ) {
            $data['booking_amount'] = 0.00;
        } else {
            $data['booking_amount'] = $data['booking_amount'] ?? 0.00;
        }

        $data['bag_rate'] = $data['bag_rate'] ?? 0.00;

        // Update bag calculations if bag quantity changed
        if (isset($data['bag_quantity'])) {
            $this->updateBagCalculations($SeedsBooking, $data['bag_quantity']);
        }
        $data['created_by'] = auth()->user()->id;
        $SeedsBooking->update($data);

        // Send SMS after successful seedbooking update
        try {
            $phone = $SeedsBooking->farmer->user->phone ?? null;
            $templateId = config('services.sms.seed_booking_template_id');
            $variables = [
                $SeedsBooking->farmer->name,
                $SeedsBooking->company->name,
                $SeedsBooking->seedVariety->name,
                $SeedsBooking->bag_quantity,
                $SeedsBooking->bag_rate,
                $SeedsBooking->booking_amount,
                $SeedsBooking->created_at->format(env('DATE_FORMATE'))
            ];
            if ($phone) {
                $this->smsService->sendTemplateSms($phone, $templateId, $variables);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send seed booking SMS', [
                'error' => $e->getMessage(),
                'booking_id' => $SeedsBooking->id ?? null
            ]);
        }

        // $SeedsBooking->update($data);
        return $SeedsBooking->load(['farmer', 'company', 'seedVariety']);
    }

    private function updateBagCalculations(SeedsBooking $seedsBooking, int $newBagQuantity): void
    {
        // Get total distributed quantity for this booking
        $totalDistributed = $seedsBooking->seedDistributions()->sum('bag_quantity');
        
        // Update the seeds booking record
        $seedsBooking->received_bags = $totalDistributed;
        $seedsBooking->pending_bags = $newBagQuantity - $totalDistributed;
        $seedsBooking->save();
        
        // Get all distributions ordered by date and ID to maintain chronological order
        $distributions = $seedsBooking->seedDistributions()
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
    }

    private function validateBagQuantityUpdate(SeedsBooking $seedsBooking, int $newBagQuantity): void
    {
        // Get total distributed quantity for this booking
        $totalDistributed = $seedsBooking->seedDistributions()->sum('bag_quantity');
        
        // If there are distributions and new quantity is less than distributed amount
        if ($totalDistributed > 0 && $newBagQuantity < $totalDistributed) {
            throw new \Exception("Cannot reduce bag quantity to {$newBagQuantity} because {$totalDistributed} bags have already been distributed. Minimum allowed quantity: {$totalDistributed}");
        }
        
        // Basic validation
        if ($newBagQuantity <= 0) {
            throw new \Exception("Bag quantity must be greater than 0");
        }
    }

    private function validateDuplicate(array $data, ?int $excludeId = null): void
    {
        // Check for duplicate booking with same company
        $query = SeedsBooking::where('farmer_id', $data['farmer_id'])
            ->where('company_id', $data['company_id'])
            ->where('seed_variety_id', $data['seed_variety_id'])
            ->where('status', $data['status']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw new \Exception("A booking already exists for this farmer with the selected company and seed variety.");
        }
    }

    public function delete(int $id): void
    {
        try {
            $seedsBooking = SeedsBooking::findOrFail($id);

            \DB::transaction(function () use ($seedsBooking) {
                // 1. Permanently delete all associated records first

                // Permanently delete seed distributions for this seeds booking
                $seedsBooking->seedDistributions()->forceDelete();

                // 2. Permanently delete the seeds booking record
                $seedsBooking->forceDelete();
            });
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting seeds booking: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete seeds booking and associated records: ' . $e->getMessage());
        }
    }

    public function searchAndSort(
        string $search = '',
        string $sortField = 'id',
        string $sortDirection = 'asc',
        int $perPage = 10,
        ?array $financialYear = null
    ): LengthAwarePaginator {
        $seedBookingQuery = SeedsBooking::with(['farmer', 'farmer.user', 'company', 'seedVariety', 'creator']);

        if( !empty( $financialYear ) ) {
            $seedBookingQuery->when($financialYear, function ($q) use ($financialYear) {
                $startDate = \Carbon\Carbon::parse($financialYear['startDate'])->startOfDay();
                $endDate = \Carbon\Carbon::parse($financialYear['endDate'])->endOfDay();
                
                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        return $seedBookingQuery->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->Where('bag_quantity', 'like', "%{$search}%")
                  ->orWhere('status', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            })
            ->orWhereHas('farmer', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                ->orWhere('village_name', 'like', "%{$search}%")
                ->orWhere('farmer_id', 'like', "%{$search}%");
            })

            // Farmer -> User relation
            ->orWhereHas('farmer.user', function ($q) use ($search) {
                $q->where('phone', 'like', "%{$search}%");
            })
            
            ->orWhereHas('company', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orWhereHas('seedVariety', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        })
        ->orderBy($sortField, $sortDirection)
        ->paginate($perPage);
    }


    public function getSeedsBookingReportData($financialYear = null)
    {
        $query = SeedsBooking::with(['farmer', 'farmer.user', 'company', 'seedVariety']);
        if( !empty( $financialYear ) ) {
            $query->when($financialYear, function ($q) use ($financialYear) {
                $startDate = \Carbon\Carbon::parse($financialYear['startDate'])->startOfDay();
                $endDate = \Carbon\Carbon::parse($financialYear['endDate'])->endOfDay();

                $q->whereBetween('created_at', [$startDate, $endDate]);
            });
        }

        return $query->orderBy('id', 'desc')
            ->get();
    }

}
