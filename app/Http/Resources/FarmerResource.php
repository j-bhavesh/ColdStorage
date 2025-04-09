<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="FarmerResource",
 *     title="Farmer Resource",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="farmer_id", type="string", example="FARM123"),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="village_name", type="string", example="Village A"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T10:00:00Z")
 * )
 */
class FarmerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'farmer_id' => $this->farmer_id,
            'name' => $this->name,
            'village_name' => $this->village_name,
            'phone' => $this->user->phone,
            // 'user_id' => $this->user_id,
            // Relationship info
            'created_by' => $this->creator ? [
                'id' => $this->creator->id,
                'name' => $this->creator->name,
                'email' => $this->creator->email,
            ] : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 