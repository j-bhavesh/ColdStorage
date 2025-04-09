<?php

namespace App\Services;

use App\Models\AdvancePayment;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Services\SmsService;

use Barryvdh\DomPDF\Facade\Pdf;

class AdvancePaymentService
{
    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Get all advance payments with pagination and search
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

        $query = AdvancePayment::with(['farmer', 'creator']);

        if( !empty( $financialYear ) ) {
            $query->when($financialYear, function ($q) use ($financialYear) {
                $startDate = \Carbon\Carbon::parse($financialYear['startDate'])->startOfDay();
                $endDate = \Carbon\Carbon::parse($financialYear['endDate'])->endOfDay();

                $q->whereBetween('payment_date', [$startDate, $endDate]);
            });
        }

        if ($search) {
            $query->where(function ($query) use ($search) {
                $query->whereHas('farmer', function ($q) use ($search) {
                    
                    $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('farmer_id', 'like', '%' . $search . '%');
                    
                })
                ->orWhere('amount', 'like', '%' . $search . '%')
                ->orWhere('payment_date', 'like', '%' . $search . '%')
                ->orWhere('taken_by', 'like', '%' . $search . '%')
                ->orWhere('taken_by_name', 'like', '%' . $search . '%');
            });
        }

        if ($sortField && in_array(strtolower($sortDirection ?? ''), ['asc', 'desc'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        return $query->paginate($perPage);
    }

    /**
     * Get a single advance payment by ID
     *
     * @param int $id
     * @return AdvancePayment
     */
    public function findById(int $id): AdvancePayment
    {
        return AdvancePayment::with(['farmer', 'farmer.user', 'creator'])->findOrFail($id);
    }

    /**
     * Check if an advance payment is duplicate
     *
     * @param array $data
     * @param int|null $excludeId
     * @return bool
     */
    private function isDuplicate(array $data, ?int $excludeId = null): bool
    {
        $query = AdvancePayment::where('farmer_id', $data['farmer_id'])
            ->where('amount', $data['amount'])
            ->where('payment_date', $data['payment_date']);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Create a new advance payment
     *
     * @param array $data
     * @return AdvancePayment
     * @throws \Exception
     */
    public function create(array $data): AdvancePayment
    {
        // if ($this->isDuplicate($data)) {
        //     throw new \Exception('Duplicate advance payment detected. Same amount and payment date already exists for this agreement.');
        // }

        // If taken_by is self, remove taken_by_name
        if (isset($data['taken_by']) && strtolower($data['taken_by']) === 'self') {
            $data['taken_by_name'] = '';
            // unset($data['taken_by_name']);
        }

        $data['created_by'] = auth()->user()->id;
        $advancePayment = AdvancePayment::create($data);
        $advancePayment->load(['farmer']);

        // Send SMS after successful payment
        try {
            $phone = $advancePayment->farmer->user->phone ?? null;
            // $templateId = '1707174973459606348';
            $templateId = config('services.sms.advance_payment_template_id');   
            $variables = [
                $advancePayment->farmer->name,
                $advancePayment->amount,
                $advancePayment->payment_date->format(env('DATE_FORMATE')),
                $advancePayment->taken_by === 'self' ? 'Self' : $advancePayment->taken_by_name
            ];
            if ($phone) {
                $this->smsService->sendTemplateSms($phone, $templateId, $variables);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send advance payment SMS', [
                'error' => $e->getMessage(),
                'payment_id' => $advancePayment->id ?? null
            ]);
        }

        return $advancePayment;
    }

    /**
     * Update an existing advance payment
     *
     * @param int $id
     * @param array $data
     * @return AdvancePayment
     * @throws \Exception
     */
    public function update(int $id, array $data): AdvancePayment
    {
        // if ($this->isDuplicate($data, $id)) {
        //     throw new \Exception('Duplicate advance payment detected. Same amount and payment date already exists for this agreement.');
        // }

        // If taken_by is self, remove taken_by_name
        if (isset($data['taken_by']) && strtolower($data['taken_by']) === 'self') {
            $data['taken_by_name'] = '';
            // unset($data['taken_by_name']);
        }

        $advancePayment = $this->findById($id);

        $data['created_by'] = auth()->user()->id;

        $advancePayment->update($data);

        // Send SMS after successful payment
        try {
            $phone = $advancePayment->farmer->user->phone ?? null;
            // $templateId = '1707174973459606348';
            $templateId = config('services.sms.advance_payment_template_id');   
            $variables = [
                $advancePayment->farmer->name,
                $advancePayment->amount,
                $advancePayment->payment_date->format(env('DATE_FORMATE')),
                $advancePayment->taken_by === 'self' ? 'Self' : $advancePayment->taken_by_name
            ];
            if ($phone) {
                $this->smsService->sendTemplateSms($phone, $templateId, $variables);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to send advance payment SMS', [
                'error' => $e->getMessage(),
                'payment_id' => $advancePayment->id ?? null
            ]);
        }
        
        return $advancePayment->fresh(['farmer']);
    }

    /**
     * Delete an advance payment
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            $advancePayment = $this->findById($id);
            \DB::transaction(function () use ($advancePayment) {
                $advancePayment->forceDelete();
            });
            return true;
        } catch (\Exception $e) {
            \Log::error('Error permanently deleting advance payment: ' . $e->getMessage());
            throw new \Exception('Failed to permanently delete advance payment: ' . $e->getMessage());
        }
    }

    /**
     * Filter advance payments with multiple criteria
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function filter(array $filters): LengthAwarePaginator
    {
        $query = AdvancePayment::query()->with(['farmer', 'creator']);

        // Filter by farmer_id
        if (!empty($filters['farmer_id'])) {
            $query->where('farmer_id', $filters['farmer_id']);
        }

        // Filter by farmer name
        if (!empty($filters['farmer_name'])) {
            $query->whereHas('farmer', function ($q) use ($filters) {
                $q->where('name', 'like', '%' . $filters['farmer_name'] . '%');
            });
        }

        // Filter by date range
        if (!empty($filters['start_date'])) {
            $query->where('payment_date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $query->where('payment_date', '<=', $filters['end_date']);
        }

        // Filter by taken_by
        if (!empty($filters['taken_by'])) {
            $query->where('taken_by', $filters['taken_by']);
        }

        if (!empty($filters['taken_by_name'])) {
            $query->where('taken_by_name', $filters['taken_by_name']);
        }   

        // Apply sorting
        $sortField = $filters['sort_field'] ?? 'created_at';
        $sortDirection = strtolower($filters['sort_direction'] ?? 'desc');
        
        if (in_array($sortDirection, ['asc', 'desc'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->latest();
        }

        return $query->paginate($filters['per_page']);
    }


    /**
     * Download perticuler seed distribution in pdf
     *
     * @return Collection
     */
    public function downloadAdvancePaymentPDF($id)
    {
        $apPdf = $this->findById($id);
        
        // $sdPdf = SeedDistribution::with(['seedsBooking', 'farmer', 'farmer.user', 'seedVariety', 'company'])->find($id);
        
        $fileName = 'advance-payment-' . $id . '.pdf';
        // $filePath = storage_path('app/public/challans/' . $fileName);

        // // Ensure directory exists
        // if (!file_exists(dirname($filePath))) {
        //     mkdir(dirname($filePath), 0777, true);
        // }

        // file_put_contents($filePath, $pdf);

        // load pdf view (resources/views/pdf/challan.blade.php)
        $pdf = Pdf::loadView('admin.advance-payments.advance-payment-pdf', compact('apPdf'));

        // $url = asset('storage/challans/' . $fileName);
        return $pdf->output();
    }
}