<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgreementResource extends JsonResource
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
            'farmer_id' => $this->farmer_id,
            'farmer' => new FarmerResource($this->whenLoaded('farmer')),
            'seed_variety_id' => $this->seed_variety_id,
            'seed_variety' => new SeedVarietyResource($this->whenLoaded('seedVariety')),
            'rate_per_kg' => (float)$this->rate_per_kg,
            'agreement_date' => $this->agreement_date,
            'vighas' => $this->vighas,
            'bag_quantity' => (int)$this->bag_quantity,
            'received_bags' => (int)$this->received_bags,
            'pending_bags' => (int)$this->pending_bags,
            'surplus_bags' => (int)$this->surplus_bags,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 