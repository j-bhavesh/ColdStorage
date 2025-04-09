<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
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
            'transporter_id' => $this->transporter_id,
            'vehicle_number' => $this->vehicle_number,
            'vehicle_type' => $this->vehicle_type,
            'capacity' => $this->capacity,
            'transporter' => new TransporterResource($this->whenLoaded('transporter')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 