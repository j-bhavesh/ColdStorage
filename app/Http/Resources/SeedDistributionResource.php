<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeedDistributionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'seeds_booking_id' => $this->seeds_booking_id,
            'farmer_id' => $this->farmer_id,
            'seed_variety_id' => $this->seed_variety_id,
            'company_id' => $this->company_id,
            'bag_quantity' => (int)$this->bag_quantity,
            'pending_bags' => (int)$this->pending_bags,
            'distribution_date' => $this->distribution_date->format('Y-m-d'),
            'vehicle_number' => $this->vehicle_number,
            'received_by' => $this->received_by,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'seeds_booking' => new SeedsBookingResource($this->whenLoaded('seedsBooking')),
            'farmer' => new FarmerResource($this->whenLoaded('farmer')),
            'seed_variety' => new SeedVarietyResource($this->whenLoaded('seedVariety')),
            'company' => new CompanyResource($this->whenLoaded('company')),
        ];
    }
} 