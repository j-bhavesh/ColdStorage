<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\StorageUnloadingRequest;
use App\Http\Resources\StorageUnloadingResource;
use App\Models\StorageUnloading;
use App\Services\StorageUnloadingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StorageUnloadingsController extends ApiController
{
    protected $storageUnloadingService;

    public function __construct(StorageUnloadingService $storageUnloadingService)
    {
        $this->storageUnloadingService = $storageUnloadingService;
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

        $storageUnloadings = $this->storageUnloadingService->getAll(
            $search,
            $sortField,
            $sortDirection,
            $perPage
        );

        return $this->successResponse(
            StorageUnloadingResource::collection($storageUnloadings),
            'Storage Unloadings retrieved successfully'
        );
    }

    public function store(StorageUnloadingRequest $request)
    {
        try {
            $storageUnloading = $this->storageUnloadingService->create($request->validated());

            $storageUnloading->load(['transporter', 'vehicle','coldStorage','seedVariety']);

            return $this->successResponse(
                new StorageUnloadingResource($storageUnloading),
                'Storage Unloading created successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'An error occurred while creating the Storage Unloading: ' . $e->getMessage(),
                200
            );
        }
    }

    public function show($id)
    {
        try {
            $storageUnloading = StorageUnloading::with(['transporter', 'vehicle','coldStorage','seedVariety'])
                ->findOrFail($id);
            
            if (!$storageUnloading) {
                return $this->errorResponse('Storage Unloading not found', 200);
            }
            
            return $this->successResponse(
                new StorageUnloadingResource($storageUnloading),
                'Storage Unloading retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving Storage Unloading: ' . $e->getMessage(), 200);
        }
    }

    public function update(StorageUnloadingRequest $request,int $id)
    {
        try {
            
            //$storageUnloading = StorageUnloading::findOrFail($id);
            
            if (empty($request->validated())) {
                return $this->errorResponse('No data provided for update', 200);
            }

            $storageUnloading = $this->storageUnloadingService->update($id, $request->validated());

            return $this->successResponse(
                new StorageUnloadingResource($storageUnloading),
                'Storage Unloading updated successfully'
            );

        } catch (\Exception $e){

            return $this->errorResponse('An error occurred while updating the Storage Unloading: '.$e->getMessage(), 200);

        }
    }

    public function destroy(int $id)
    {
        try {
            $this->storageUnloadingService->delete($id);
            return $this->successResponse(
                null,
                'Storage Unloading deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while deleting the Storage Unloading', 200);
        }
    }

    public function downloadStorageUnloadingPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sul_id' => 'required|exists:storage_unloadings,id'
        ],
        [
            'sul_id.required' => 'Storage unloading is required.',
            'sul_id.exists' => 'Storage unloading is not exists.'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 200);
        }


        try {
            // Get PDF bytes from service
            $pdfContent = $this->storageUnloadingService->downloadStorageUnloadingPDF($request->sul_id);
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while downloading the storage unloading: '.$e->getMessage(), 200);
        }
     
        $fileName = 'storage-unloading-'.$request->sul_id.'-'. now()->format('Ymd-His') .'.pdf';
     
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