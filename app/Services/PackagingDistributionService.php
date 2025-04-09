<?php

namespace App\Services;

use App\Models\PackagingDistribution;
use App\Models\Agreement;
use App\Services\SmsService;

use Barryvdh\DomPDF\Facade\Pdf;

class PackagingDistributionService
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
        ?array $financialYear = null
    )
    {
        $query = PackagingDistribution::with(['agreement.farmer', 'agreement.farmerUser', 'agreement.seedVariety', 'creator']);

        if( !empty( $financialYear ) ) {
            $query->when($financialYear, function ($q) use ($financialYear) {
                $startDate = \Carbon\Carbon::parse($financialYear['startDate'])->startOfDay();
                $endDate = \Carbon\Carbon::parse($financialYear['endDate'])->endOfDay();

                $q->whereBetween('distribution_date', [$startDate, $endDate]);
            });
        }
        
        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('agreement.farmer', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('farmer_id', 'like', '%' . $search . '%')
                    ->orWhere('village_name', 'like', '%' . $search . '%');
                })
                ->orWhereHas('agreement.farmerUser', function ($q) use ($search) {
                    $q->where('phone', 'like', '%' . $search . '%');
                })
                ->orWhereHas('agreement.seedVariety', function ($q) use ($search) {
                    $q->where('name', 'like', '%' . $search . '%');
                })
                ->orWhere('vehicle_number', 'like', '%' . $search . '%')
                ->orWhere('bag_quantity', 'like', '%' . $search . '%')
                ->orWhere('distribution_date', 'like', '%' . $search . '%')
                ->orWhere('received_by', 'like', '%' . $search . '%');
            });
        }

        if ($sortField && in_array(strtolower($sortDirection ?? ''), ['asc', 'desc'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    public function searchPdFarmer($searchPdFarmer)
    {
        $pdFarmerResults = Agreement::with(['farmer', 'seedVariety'])
        ->when($searchPdFarmer, function ($query) use ($searchPdFarmer) {
            $query->whereHas('farmer', function ($q) use ($searchPdFarmer) {
                $q->where('name', 'like', "%{$searchPdFarmer}%")
                  ->orWhere('id', 'like', "%{$searchPdFarmer}%")    // farmer_id search
                  ->orWhere('farmer_id', 'like', "%{$searchPdFarmer}%"); // if you have farmer_code field
            })
            ->orWhereHas('seedVariety', function ($q) use ($searchPdFarmer) {
                $q->where('name', 'like', "%{$searchPdFarmer}%");
            });
        })
        ->get()
        ->map(function ($agreement) {

            $distributed = PackagingDistribution::where('agreement_id', $agreement->id)->sum('bag_quantity');
            $remaining = $agreement->bag_quantity - $distributed;

            return [
                'agreement_id' => $agreement->id,
                'farmer_id' => $agreement->farmer->farmer_id,
                'farmer_name' => $agreement->farmer->name,
                'seed_variety_name' => $agreement->seedVariety->name,
                'remaining_quantity' => $remaining,
            ];
        })
        ->filter(function ($farmer) {
            // Apply same Blade if-condition
            return (empty($farmer) && $farmer['remaining_quantity'] > 0)
                || (!empty($farmer));
        })
        ->map(function ($farmer) {
            return [
                'id' => $farmer['agreement_id'],
                'text' => $farmer['farmer_name'] . ' (' . $farmer['farmer_id'] . ') - ' .
                          $farmer['seed_variety_name'] . ' - ' .
                          'Agreement #' . $farmer['agreement_id'] . ' - ' .
                          'Remaining: ' . $farmer['remaining_quantity']
            ];
        });

        return $pdFarmerResults;
    }

    public function create(array $data)
    {
        // First validate the remaining quantity
        $this->validateDuplicate($data);

        // Then check for duplicate distribution on the same date
        $existingDistribution = PackagingDistribution::where('agreement_id', $data['agreement_id'])->first();
        
        /*if ($existingDistribution) {
            throw new \Exception('A distribution record already exists for this agreement on the selected date. Please select a different date.');
        }*/

        // Get the agreement data
        $agreement = Agreement::findOrFail($data['agreement_id']);

        // Total bags from the agreement
        $totalAgreementBags = $agreement->bag_quantity;

        // Calculate the pending bags
        if( !empty( $existingDistribution ) ) {
            $totalExistingSuppliedBags = PackagingDistribution::where('agreement_id', $data['agreement_id'])->sum('bag_quantity');

            // $pendingBags = $existingDistribution->pending_bags - $data['bag_quantity'];
            // Sum all existing supplied bags
            // $totalExistingSuppliedBags = $existingDistribution->sum('bag_quantity');
            // Add current supplied bags to existing
            $totalSuppliedBags = $totalExistingSuppliedBags + $data['bag_quantity'];
            $pendingBags = $totalAgreementBags - $totalSuppliedBags;
        } else {
            // $pendingBags = $totalAgreementBags - $data['bag_quantity'];
            // First time distribution for this agreement
            $totalSuppliedBags = $data['bag_quantity'];
            $pendingBags = $totalAgreementBags - $data['bag_quantity'];
        }

        // Set pending bags for packaging_distribution tble
        $data['pending_bags'] = $pendingBags;
        $data['received_bags'] = $data['bag_quantity'];

        $data['created_by'] = auth()->user()->id;

        $distribution = PackagingDistribution::create($data);
        $distribution->load(['agreement.farmer']);

        try {
            $phone = $distribution->agreement->farmer->user->phone ?? null;
            // $templateId = '1707174973531241476';
            $templateId = config('services.sms.packaging_distribution_template_id');
            $variables = [
                $distribution->agreement->farmer->name,
                $distribution->bag_quantity,
                $distribution->vehicle_number,
                $distribution->distribution_date->format(env('DATE_FORMATE')),
                $distribution->received_by
            ];
            if ($phone) {
                $this->smsService->sendTemplateSms($phone, $templateId, $variables);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send packaging distribution SMS', [
                'error' => $e->getMessage(),
                'distribution_id' => $distribution->id ?? null
            ]);
        }

        // return PackagingDistribution::create($data);
        return $distribution;
    }

    public function update($id, array $data)
    {
        $this->validateDuplicate($data, $id);

        // Get the packaging distribution data by id
        $distribution = $this->findById($id);

        // Get the agreement data
        $agreement = Agreement::findOrFail($data['agreement_id']);

        // Total bags from the agreement
        $totalAgreementBags = $agreement->bag_quantity;
        $oldBagQuantity = $distribution->bag_quantity;

        // Calculate the pending bags
        // Calculate the pending bags
        if( !empty( $distribution ) ) {
            $currentTotalSupplied = PackagingDistribution::where('agreement_id', $data['agreement_id'])->sum('bag_quantity');
            // $pendingBags = $distribution->pending_bags - $data['bag_quantity'];
            // Calculate current total supplied bags
            // $currentTotalSupplied = $distribution->sum('bag_quantity');
            // Subtract old quantity and add new quantity
            $newTotalSupplied = $currentTotalSupplied - $oldBagQuantity + $data['bag_quantity'];
            $pendingBags = $totalAgreementBags - $newTotalSupplied;
        } else {
            // $pendingBags = $totalAgreementBags - $data['bag_quantity'];
            // This shouldn't happen in update, but handle it
            $pendingBags = $totalAgreementBags - $data['bag_quantity'];
        }
        // $pendingBags = $totalAgreementBags - $data['bag_quantity'];

        // Set pending bags for packaging_distribution tble
        $data['pending_bags'] = $pendingBags;
        $data['received_bags'] = $data['bag_quantity'];

        $data['created_by'] = auth()->user()->id;

        $distribution->update($data);

        // Send SMS after successful distribution
        try {
            $phone = $distribution->agreement->farmer->user->phone ?? null;
            // $templateId = '1707174973531241476';
            $templateId = config('services.sms.packaging_distribution_template_id');
            $variables = [
                $distribution->agreement->farmer->name,
                $distribution->bag_quantity,
                $distribution->vehicle_number,
                $distribution->distribution_date->format(env('DATE_FORMATE')),
                $distribution->received_by
            ];
            if ($phone) {
                $this->smsService->sendTemplateSms($phone, $templateId, $variables);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send packaging distribution SMS', [
                'error' => $e->getMessage(),
                'distribution_id' => $distribution->id ?? null
            ]);
        }

        return $distribution;
    }

    private function validateDuplicate(array $data, ?int $excludeId = null): void
    {
        // Check if the agreement exists and is active
        $agreement = Agreement::where('id', $data['agreement_id'])
            // ->where('status', 'active')
            ->first();

        if (!$agreement) {
            throw new \Exception('No active agreement found.');
        }

        // Get total distributed quantity for this agreement
        $totalDistributed = PackagingDistribution::where('agreement_id', $data['agreement_id'])
            ->when($excludeId, function ($query) use ($excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->sum('bag_quantity');

        // Calculate remaining quantity from agreement
        $remainingQuantity = $agreement->bag_quantity - $totalDistributed;

        // If all bags are already distributed
        if ($remainingQuantity <= 0) {
            throw new \Exception("All bags from this agreement have been distributed. Cannot create new distribution.");
        }

        // If trying to distribute more than remaining quantity
        if ($data['bag_quantity'] > $remainingQuantity) {
            throw new \Exception("Cannot distribute more than remaining quantity. Remaining bags: {$remainingQuantity}");
        }

        // If bag quantity is 0 or negative
        if ($data['bag_quantity'] <= 0) {
            throw new \Exception("Bag quantity must be greater than 0");
        }
    }

    public function delete($id)
    {
        try {
            $distribution = $this->findById($id);
            \DB::transaction(function () use ($distribution) {
                $distribution->forceDelete();
            });
            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting packaging distribution: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete packaging distribution: ' . $e->getMessage());
        }
    }

    public function findById($id)
    {
        return PackagingDistribution::with(['agreement.farmer', 'agreement.farmerUser', 'agreement.seedVariety', 'creator'])->findOrFail($id);
    }

    public function getFarmersWithActiveAgreements()
    {
        return Agreement::with(['farmer', 'seedVariety'])
            // ->where('status', 'active')
            ->get()
            ->map(function ($agreement) {
                return [
                    'agreement_id' => $agreement->id,
                    'farmer_id' => $agreement->farmer->farmer_id,
                    'farmer_name' => $agreement->farmer->name,
                    'seed_variety_name' => $agreement->seedVariety->name,
                    'remaining_quantity' => $agreement->bag_quantity - PackagingDistribution::where('agreement_id', $agreement->id)->sum('bag_quantity')
                ];
            });
    }

    /**
     * Download perticuler seed distribution in pdf
     *
     * @return Collection
     */
    public function downloadPackagingDistributionPDF($id)
    {
        $pdPdf = $this->findById($id);
        // $sdPdf = SeedDistribution::with(['seedsBooking', 'farmer', 'farmer.user', 'seedVariety', 'company'])->find($id);
        
        $fileName = 'packaging-distribution-' . $id . '.pdf';
        // $filePath = storage_path('app/public/challans/' . $fileName);

        // // Ensure directory exists
        // if (!file_exists(dirname($filePath))) {
        //     mkdir(dirname($filePath), 0777, true);
        // }

        // file_put_contents($filePath, $pdf);

        // load pdf view (resources/views/pdf/challan.blade.php)
        $pdf = Pdf::loadView('admin.packaging-distributions.packaging-distribution-pdf', compact('pdPdf'));

        // $url = asset('storage/challans/' . $fileName);
        return $pdf->output();
    }
}
