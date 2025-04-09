<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\FarmerRequest;
use App\Http\Resources\FarmerResource;
use App\Models\Farmer;
use App\Services\FarmerService;
use Illuminate\Http\Request;

class FarmerController extends ApiController
{
    protected $farmerService;

    public function __construct(FarmerService $farmerService)
    {
        $this->farmerService = $farmerService;
    }

    /**
     * @OA\Get(
     *     path="/api/v1/farmers",
     *     summary="Get all farmers",
     *     tags={"Farmers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Farmers retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Farmers retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Farmer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search'); 
        $farmers = $this->farmerService->getAllFarmers($perPage, $search);
        return $this->successResponse(
            FarmerResource::collection($farmers),
            'Farmers retrieved successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/api/v1/farmers",
     *     summary="Create a new farmer",
     *     tags={"Farmers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FarmerRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Farmer created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Farmer created successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Farmer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function store(FarmerRequest $request)
    {
        try {
            $result = $this->farmerService->createFarmer($request->validated());
            return $this->successResponse(
                new FarmerResource($result['farmer']),
                    $result['is_new'] ? 'Farmer created successfully' : 'Farmer already exists',
                    $result['is_new'] ? 200 : 200
            );
        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while addindg the farmer: '.$e->getMessage(),
                // 'errors' => $e->getMessage()
            ], 200);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/v1/farmers/{farmer}",
     *     summary="Get a specific farmer",
     *     tags={"Farmers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="farmer",
     *         in="path",
     *         required=true,
     *         description="Farmer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Farmer retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Farmer retrieved successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Farmer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Farmer not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function show(Farmer $farmer)
    {
        return $this->successResponse(
            new FarmerResource($farmer),
            'Farmer retrieved successfully'
        );
    }

    /**
     * @OA\Put(
     *     path="/api/v1/farmers/{farmer}",
     *     summary="Update a farmer",
     *     tags={"Farmers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="farmer",
     *         in="path",
     *         required=true,
     *         description="Farmer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FarmerRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Farmer updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Farmer updated successfully"),
     *             @OA\Property(property="data", ref="#/components/schemas/Farmer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Farmer not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function update(FarmerRequest $request, $id)
    {
        try {
            $farmer = Farmer::findOrFail($id);
            
            // Get data from request
            $updateData = [];
            
            if ($request->has('name')) {
                $updateData['name'] = $request->input('name');
            }
            
            if ($request->has('village_name')) {
                $updateData['village_name'] = $request->input('village_name');
            }
            
            if ($request->has('phone')) {
                $updateData['phone'] = $request->input('phone');
            }
            
            if (empty($updateData)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data provided for update'
                ], 200);
            }
            
            $farmer = $this->farmerService->updateFarmer($farmer, $updateData);
            
            return $this->successResponse(
                new FarmerResource($farmer),
                'Farmer updated successfully'
            );
        } catch (\Exception $e) {
            \Log::error('Update error:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the farmer: '.$e->getMessage(),
                'debug' => $e->getMessage()
            ], 200);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/farmers/{farmer}",
     *     summary="Delete a farmer",
     *     tags={"Farmers"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="farmer",
     *         in="path",
     *         required=true,
     *         description="Farmer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Farmer deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Farmer deleted successfully"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Farmer not found"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function destroy(Farmer $farmer)
    {
        $this->farmerService->deleteFarmer($farmer);
        return $this->successResponse(
            null,
            'Farmer deleted successfully'
        );
    }
} 