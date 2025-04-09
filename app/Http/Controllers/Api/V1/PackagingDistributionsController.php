<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\PackagingDistributionRequest;
use App\Http\Resources\PackagingDistributionResource;
use App\Services\PackagingDistributionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PackagingDistributionsController extends ApiController
{
    protected $packagingDistributionService;

    public function __construct(PackagingDistributionService $packagingDistributionService)
    {
        $this->packagingDistributionService = $packagingDistributionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->only([
            'agreement_id',
            'bag_quantity',
            'vehicle_number',
            'distribution_date',
            'received_by'
        ]);

        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $distributions = $this->packagingDistributionService->getAll($search, '', '', $perPage);

        return $this->successResponse(
            PackagingDistributionResource::collection($distributions),
            'Packaging distributions retrieved successfully'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PackagingDistributionRequest $request)
    { 
        try {
            $distribution = $this->packagingDistributionService->create($request->validated());

            $distribution->load(['agreement.farmer', 'agreement.seedVariety']);

            return $this->successResponse(
                new PackagingDistributionResource($distribution),
                'Packaging distribution created successfully'
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
        $distribution = $this->packagingDistributionService->findById($id);
        
        if (!$distribution) {
            return $this->errorResponse('Packaging distribution not found', 200);
        }

        return $this->successResponse(
            new PackagingDistributionResource($distribution)
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PackagingDistributionRequest $request, int $id)
    {
        try {
            $distribution = $this->packagingDistributionService->update($id, $request->validated());
            
            if (!$distribution) {
                return $this->errorResponse('Packaging distribution not found', 200);
            }

            return $this->successResponse(
                new PackagingDistributionResource($distribution),
                'Packaging distribution updated successfully'
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
        $deleted = $this->packagingDistributionService->delete($id);
        
        if (!$deleted) {
            return $this->errorResponse('Packaging distribution is not found', 200);
        }

        return $this->successResponse(
            null,
            'Packaging distribution deleted successfully'
        );
    }


    public function downloadPackagingDistributionPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pd_id' => 'required|exists:packaging_distributions,id'
        ],
        [
            'pd_id.required' => 'Packaging Distribution is required.',
            'pd_id.exists' => 'Packaging Distribution is not exists.'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 200);
        }


        try {
            // Get PDF bytes from service
            $pdfContent = $this->packagingDistributionService->downloadPackagingDistributionPDF($request->pd_id);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while downloading the seed distribution: '.$e->getMessage(), 200);
        }
     
        $fileName = 'seed-distribution-'.$request->pd_id.'-'. now()->format('Ymd-His') .'.pdf';
     
        // Stream PDF response
        return response()->stream(function () use ($pdfContent) {
            echo $pdfContent;
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
            'Content-Length' => strlen($pdfContent),
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}