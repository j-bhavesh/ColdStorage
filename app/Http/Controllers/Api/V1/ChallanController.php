<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\ChallanRequest;
use App\Http\Resources\ChallanResource;
use App\Models\Challan;
use App\Services\ChallanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChallanController extends ApiController
{
    protected $challanService;

    public function __construct(ChallanService $challanService)
    {
        $this->challanService = $challanService;
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

        $challans = $this->challanService->getAll(
            $search,
            $sortField,
            $sortDirection,
            $perPage
        );

        return $this->successResponse(
            ChallanResource::collection($challans),
            'Challans retrieved successfully'
        );
    }

    public function store(ChallanRequest $request)
    {
        try {
            $challan = $this->challanService->create($request->validated());
            return $this->successResponse(
                new ChallanResource($challan),
                'Challan created successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'An error occurred while creating the challan: ' . $e->getMessage(),
                200
            );
        }
    }

    public function show($id) 
    {
        try {
            $challan = Challan::with(['farmer', 'vehicle'])
                ->findOrFail($id);
            
            if (!$challan) {
                return $this->errorResponse('Challan not found', 200);
            }

            return $this->successResponse(
                new ChallanResource($challan),
                'Challan retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving challan: ' . $e->getMessage(), 200);
        }
    }

    public function update(ChallanRequest $request, int $id)
    {
        try {
            if (empty($request->validated())) {
                return $this->errorResponse('No data provided for update', 200);
            }

            $challan = $this->challanService->update($id, $request->validated());

            return $this->successResponse(
                new ChallanResource($challan),
                'Challan updated successfully'
            );

        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while updating the challan: ' . $e->getMessage(), 200);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->challanService->delete($id);
            return $this->successResponse(
                null,
                'Challan deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while deleting the challan', 200);
        }
    }

    public function downloadChallansPdf(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'challan_id' => 'required|exists:challans,id'
        ],
        [
            'challan_id.required' => 'Challan is required.',
            'challan_id.exists' => 'Challan is not exists.'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 200);
            // return response()->json([
            //     'success' => false,
            //     'errors'  => $validator->errors()
            // ], 422);
        }

        $challan = $this->challanService->downloadChallanPDF($request->challan_id);

        // Return as file download
        return response($challan)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'attachment; filename="challan-'.$request->challan_id.'.pdf"');
    }
}