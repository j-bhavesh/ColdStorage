<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="UnloadingCompanyResource",
 *     title="Unloading Company Resource",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="name", type="string", example="ABC Unloading Co."),
 *     @OA\Property(property="contact_person", type="string", example="John Doe"),
 *     @OA\Property(property="contact_number", type="string", example="+1234567890"),
 *     @OA\Property(property="address", type="string", example="123 Business Street"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T10:00:00Z")
 * )
 */
class UnloadingCompanyResource extends JsonResource
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
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'contact_number' => $this->contact_number,
            'address' => $this->address,
            'status' => $this->status,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
} 