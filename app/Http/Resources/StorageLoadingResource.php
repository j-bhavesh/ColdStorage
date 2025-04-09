<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StorageLoadingResource extends JsonResource
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
            'agreement_id' => $this->agreement_id,
            'agreement' => new AgreementResource($this->whenLoaded('agreement')),
            'transporter_id' => $this->transporter_id,
            'transporter' => new TransporterResource($this->whenLoaded('transporter')),
            'vehicle_id' => $this->vehicle_id,
            'vehicle' => new VehicleResource($this->whenLoaded('vehicle')),
            'vehicle_number' => $this->vehicle_number,
            'cold_storage_id' => $this->cold_storage_id,
            'cold_storage' => new ColdStorageResource($this->whenLoaded('coldStorage')),
            'rst_number' => $this->rst_number,
            'chamber_no' => $this->chamber_no,
            'bag_quantity' => $this->bag_quantity,
            'pending_bags' => $this->pending_bags,
            'net_weight' => $this->net_weight,
            'extra_bags' => $this->extra_bags,
            'remarks' => $this->remarks,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
} 