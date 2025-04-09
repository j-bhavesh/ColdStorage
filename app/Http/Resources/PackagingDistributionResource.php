<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackagingDistributionResource extends JsonResource
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
            'bag_quantity' => (int)$this->bag_quantity,
            'pending_bags' => (int)$this->pending_bags,
            'vehicle_number' => $this->vehicle_number,
            'distribution_date' => $this->distribution_date->format('Y-m-d'),
            'received_by' => $this->received_by,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
} 