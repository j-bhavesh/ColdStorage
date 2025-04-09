<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\SeedDistributionRequest;
use App\Http\Resources\SeedDistributionResource;
use App\Services\SeedDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SeedDistributionsController extends ApiController
{
    protected $seedDistributionService;

    public function __construct(SeedDistributionService $seedDistributionService)
    {
        $this->seedDistributionService = $seedDistributionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'seeds_booking_id',
            'farmer_id',
            'seed_variety_id',
            'company_id',
            'distribution_date',
            'search'
        ]);

        $perPage = $request->input('per_page', 10);

        $distributions = $this->seedDistributionService->getAll($filters, $perPage);
        return $this->successResponse(
            SeedDistributionResource::collection($distributions),
            'Seeds distributions retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SeedDistributionRequest $request)
    {
        try {
            $distribution = $this->seedDistributionService->create($request->validated());
            
            // Load the relationships
            $distribution->load(['seedsBooking', 'farmer', 'seedVariety', 'company']);
            
            return $this->successResponse(
                new SeedDistributionResource($distribution),
                'Seed distribution created successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $distribution = $this->seedDistributionService->findById($id);
        
        if (!$distribution) {
            return $this->errorResponse('Seed distribution not found', 200);
        }

        return $this->successResponse(
            new SeedDistributionResource($distribution)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SeedDistributionRequest $request, int $id)
    {
        try {
            $distribution = $this->seedDistributionService->update($id, $request->validated());
            
            if (!$distribution) {
                return $this->errorResponse('Seed distribution not found', 200);
            }

            return $this->successResponse(
                new SeedDistributionResource($distribution),
                'Seed distribution updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $deleted = $this->seedDistributionService->delete($id);
        
        if (!$deleted) {
            return $this->errorResponse('Seed distribution not found', 200);
        }

        return $this->successResponse(
            null,
            'Seed distribution deleted successfully'
        );
    }

    public function downloadSeedDistributionPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sd_id' => 'required|exists:seed_distributions,id'
        ],
        [
            'sd_id.required' => 'Seeds Distribution is required.',
            'sd_id.exists' => 'Seeds Distribution is not exists.'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 200);
            // return response()->json([
            //     'success' => false,
            //     'errors'  => $validator->errors()
            // ], 422);
        }
        
        $sdPdf = $this->seedDistributionService->downloadSeedDistributionPDF($request->sd_id);

        // Return as file download
        return response($sdPdf)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="seed-distribution-'.$request->sd_id.'.pdf"');
    }
} 