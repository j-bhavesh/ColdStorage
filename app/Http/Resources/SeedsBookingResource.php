<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="SeedsBookingResource",
 *     title="SeedsBooking Resource",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="rate", type="number", format="float", example=100.50),
 *     @OA\Property(property="booking_amount", type="number", format="float", example=500.00),
 *     @OA\Property(property="booking_type", type="string", example="Full"),
 *     @OA\Property(property="bag_quantity", type="integer", example=5),
 *     @OA\Property(property="status", type="string", example="Active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T10:00:00Z"),
 *     @OA\Property(
 *         property="farmer",
 *         ref="#/components/schemas/FarmerResource"
 *     ),
 *     @OA\Property(
 *         property="company",
 *         ref="#/components/schemas/CompanyResource"
 *     ),
 *     @OA\Property(
 *         property="seed_variety",
 *         ref="#/components/schemas/SeedVarietyResource"
 *     )
 * )
 */
class SeedsBookingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'farmer_id' => $this->farmer_id,
            'company_id' => $this->company_id,
            'seed_variety_id' => $this->seed_variety_id,
            'farmer' => new FarmerResource($this->whenLoaded('farmer')),
            'company' => new CompanyResource($this->whenLoaded('company')),
            'seed_variety' => new SeedVarietyResource($this->whenLoaded('seedVariety')),
            'booking_amount' => (float)$this->booking_amount,
            'bag_rate' => (float)$this->bag_rate,
            'booking_type' => $this->booking_type,
            'bag_quantity' => (int)$this->bag_quantity,
            'received_bags' => (int)$this->received_bags,
            'pending_bags' => (int)$this->pending_bags,
            'bag_rate' => (float)$this->bag_rate,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 