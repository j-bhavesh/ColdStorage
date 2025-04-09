<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\StorageLoadingRequest;
use App\Http\Resources\StorageLoadingResource;
use App\Models\StorageLoading;
use App\Services\StorageLoadingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StorageLoadingsController extends ApiController
{
    protected $storageLoadingService;

    public function __construct(StorageLoadingService $storageLoadingService)
    {
        $this->storageLoadingService = $storageLoadingService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $sortField = $request->input('sort_field', 'id');
        $sortDirection = strtolower($request->input('sort_direction', 'desc'));
        $search = $request->input('search');

        // Validate sort direction
        if (!in_array($sortDirection, ['asc', 'desc'])) {
            $sortDirection = 'desc';
        }

        $storageLoadings = $this->storageLoadingService->getAll(
            $search,
            $sortField,
            $sortDirection,
            $perPage
        );

        return $this->successResponse(
            StorageLoadingResource::collection($storageLoadings),
            'Storage Loadings retrieved successfully'
        );
    }

    public function store(StorageLoadingRequest $request)
    {
        try {
            $storageLoading = $this->storageLoadingService->create($request->validated());
            return $this->successResponse(
                new StorageLoadingResource($storageLoading),
                'Storage Loading created successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'An error occurred while creating the storage loading: ' . $e->getMessage(),
                200
            );
        }
    }

    public function show($id) 
    {
        try {
            $storageLoading = StorageLoading::with(['agreement.farmer','agreement.seedVariety', 'transporter', 'vehicle', 'coldStorage'])
                ->findOrFail($id);
            
            if (!$storageLoading) {
                return $this->errorResponse('Storage Loading not found', 200);
            }

            return $this->successResponse(
                new StorageLoadingResource($storageLoading),
                'Storage Loading retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving storage loading: ' . $e->getMessage(), 200);
        }
    }

    public function update(StorageLoadingRequest $request,int $id)
    {
        try {
            
            //$storageLoading = StorageLoading::findOrFail($id);
            
            if (empty($request->validated())) {
                return $this->errorResponse('No data provided for update', 200);
            }

            $storageLoading = $this->storageLoadingService->update($id, $request->validated());

            return $this->successResponse(
                new StorageLoadingResource($storageLoading),
                'Storage Loading updated successfully'
            );

        } catch (\Exception $e){

            return $this->errorResponse('An error occurred while updating the storage loading: '.$e->getMessage(), 200);

        }
    }

    public function destroy(int $id)
    {
        try {
            $this->storageLoadingService->delete($id);
            return $this->successResponse(
                null,
                'Storage Loading deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while deleting the storage loading', 200);
        }
    }

    public function downloadStorageLoadingPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sl_id' => 'required|exists:storage_loadings,id'
        ],
        [
            'sl_id.required' => 'Storage Loading is required.',
            'sl_id.exists' => 'Storage Loading is not exists.'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 200);
        }


        try {
            // Get PDF bytes from service
            $pdfContent = $this->storageLoadingService->downloadStorageLoadingPDF($request->sl_id);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while downloading the storage loading: '.$e->getMessage(), 200);
        }
     
        $fileName = 'seed-distribution-'.$request->sl_id.'-'. now()->format('Ymd-His') .'.pdf';
     
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