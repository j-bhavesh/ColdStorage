<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\Company;
use App\Models\SeedVariety;
use App\Models\ColdStorage;
use App\Models\Transporter;
use App\Models\Vehicle;
use App\Models\UnloadingCompany;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Common Data",
 *     description="API endpoints for common data"
 * )
 */
class CommonDataController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/api/v1/companies",
     *     tags={"Common Data"},
     *     summary="Get list of companies",
     *     description="Returns a list of all companies with their IDs and names",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Company Name")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
   public function companies()
   {
       return Company::select('id', 'name')->get();
   }

    /**
     * @OA\Get(
     *     path="/api/v1/seed-varieties",
     *     tags={"Common Data"},
     *     summary="Get list of seed varieties",
     *     description="Returns a list of all seed varieties with their IDs and names",
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Seed Variety Name")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function seedVarieties()
    {
       return SeedVariety::select('id', 'name')->get();
    }

    public function vehicles(Request $request)
    {
        try {
            if ($request->has('transporter_id')) {
                $transporter = Transporter::where('id', $request->transporter_id)->first();

                if (!$transporter) {
                    return $this->errorResponse('Transporter not found.', 404);
                }
                $vehicles = Vehicle::where('transporter_id', $request->transporter_id)->get();
            } else {
                $vehicles = Vehicle::all();
            }

            return $this->successResponse($vehicles, 'Vehicles fetched successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch vehicles: ' . $e->getMessage(), 500);
        }
    }

    public function transporters()
    {
        try {
            $transporters = Transporter::all();
            return $this->successResponse($transporters, 'Transporters fetched successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch transporters: ' . $e->getMessage(), 500);
        }
    }

    public function coldStorages()
    {
        try {
            $storages = ColdStorage::all();
            return $this->successResponse($storages, 'Cold storages fetched successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch cold storages: ' . $e->getMessage(), 500);
        }
    }

    public function unloadingCompanies()
    {
        try {
            $unloadingCompanies = UnloadingCompany::all();
            return $this->successResponse($unloadingCompanies, 'Unloading Companies fetched successfully.');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to fetch Unloading Companies: ' . $e->getMessage(), 500);
        }
    }

}