<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\AgreementRequest;
use App\Http\Resources\AgreementResource;
use App\Models\Agreement;
use App\Services\AgreementService;
use Illuminate\Http\Request;

class AgreementController extends ApiController
{
    protected $agreementService;

    public function __construct(AgreementService $agreementService)
    {
        $this->agreementService = $agreementService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        
        $agreements = $this->agreementService->getAll($search,
        $sortField = null,$sortDirection = null,$perPage);
        return $this->successResponse(
            AgreementResource::collection($agreements),
            'Agreements retrieved successfully'
        );
    }

    public function store(AgreementRequest $request)
    {
        try {
            
            $agreement = $this->agreementService->create($request->validated());

            $agreement->load(['farmer', 'seedVariety']);

            return $this->successResponse(
                new AgreementResource($agreement),
                'Agreement created successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'An error occurred while creating the agreement: ' . $e->getMessage(),
                200
            );
        }
    }

    public function show($id)
    {
        try {
            $agreement = Agreement::with(['farmer', 'seedVariety'])->findOrFail($id);
            
            if(!$agreement){
                return $this->errorResponse('Agreement not found',200);
            }

            return $this->successResponse(
                new AgreementResource($agreement),
                'Agreement retrieved successfully'
            );
            
        } catch (\Exception $e) {
            return $this->errorResponse('Error retrieving agreement: ' . $e->getMessage(), 200);
        }
    }

    public function update(AgreementRequest $request, $id)
    {
        try {
            
            $agreement = Agreement::findOrFail($id);
            
            if (empty($request->validated())) {
                return $this->errorResponse('No data provided for update', 200);
            }

            $agreement = $this->agreementService->update($agreement, $request->validated());

            return $this->successResponse(
                new AgreementResource($agreement),
                'Agreement updated successfully'
            );

        } catch (\Exception $e){

            return $this->errorResponse('An error occurred while updating the agreement: '.$e->getMessage(), 200);

        }
    }

    public function destroy(int $id)
    {
        try {
            $this->agreementService->delete($id);
            return $this->successResponse(
                null,
                'Agreement deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse('An error occurred while deleting the agreement', 200);
        }
    }
}