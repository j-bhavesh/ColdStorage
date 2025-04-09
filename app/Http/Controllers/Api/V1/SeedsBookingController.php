<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Http\Requests\SeedsBookingRequest;
use App\Http\Resources\SeedsBookingResource;
use App\Models\SeedsBooking;
use App\Services\SeedsBookingService;
use Illuminate\Http\Request;


class SeedsBookingController extends ApiController
{
    protected $seedsBookingService;

    public function __construct(SeedsBookingService $seedsBookingService)
    {
        $this->seedsBookingService = $seedsBookingService;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        
        $bookings = $this->seedsBookingService->getAll($search, $perPage);

        return $this->successResponse(
            SeedsBookingResource::collection($bookings),
            'Seeds bookings retrieved successfully'
        );
    }

    public function store(SeedsBookingRequest $request)
    {
        try {
            $booking = $this->seedsBookingService->create($request->validated());
            return $this->successResponse(
                new SeedsBookingResource($booking),
                'Seeds booking created successfully',
                200
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to create seeds booking: ' . $e->getMessage(),
                200
            );
        }
    }

    public function show($id)
    {
        try {
            $booking = $this->seedsBookingService->getById($id);
            return $this->successResponse(
                new SeedsBookingResource($booking),
                'Seeds booking retrieved successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Seeds booking not found',
                200
            );
        }
    }

    public function update(SeedsBookingRequest $request, $id)
    {
        try {
            $booking = SeedsBooking::findOrFail($id);
            $updatedBooking = $this->seedsBookingService->update($booking, $request->validated());
            return $this->successResponse(
                new SeedsBookingResource($updatedBooking),
                'Seeds booking updated successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to update seeds booking: ' . $e->getMessage(),
                200
            );
        }
    }

    public function destroy($id)
    {
        try {
            $this->seedsBookingService->delete($id);
            return $this->successResponse(
                null,
                'Seeds booking deleted successfully'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                'Failed to delete seeds booking: ' . $e->getMessage(),
                200
            );
        }
    }
}
