<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StorageUnloadingResource extends JsonResource
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
            'company_id' => $this->company_id,
            'company' => new CompanyResource($this->whenLoaded('unloadingCompany')),
            'cold_storage_id' => $this->cold_storage_id,
            'cold_storage' => new ColdStorageResource($this->whenLoaded('coldStorage')),
            'transporter_id' => $this->transporter_id,
            'transporter' => new TransporterResource($this->whenLoaded('transporter')),
            'vehicle_id' => $this->vehicle_id,
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'vehicle_number' => $this->vehicle_number,
            'seed_variety_id' => $this->seed_variety_id,
            'seed_variety' => new SeedVarietyResource($this->whenLoaded('seedVariety')),
            'rst_no' => $this->rst_no,
            'chamber_no' => $this->chamber_no,
            'location' => $this->location,
            'bag_quantity' => (int)$this->bag_quantity,
            'weight' => (float)$this->weight,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
} 