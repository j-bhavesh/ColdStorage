<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="CompanyResource",
 *     title="Company Resource",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="ABC Company"),
 *     @OA\Property(property="address", type="string", example="123 Business Street"),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="email", type="string", format="email", example="contact@company.com"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T10:00:00Z")
 * )
 */
class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'email' => $this->email,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 