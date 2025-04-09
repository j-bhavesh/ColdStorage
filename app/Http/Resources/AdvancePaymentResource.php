<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 *     schema="AdvancePaymentResource",
 *     title="Advance Payment Resource",
 *     @OA\Property(property="id", type="integer", format="int64", example=1),
 *     @OA\Property(property="agreement_id", type="integer", format="int64", example=1),
 *     @OA\Property(property="amount", type="number", format="float", example=1000.00),
 *     @OA\Property(property="payment_date", type="string", format="date", example="2024-03-20"),
 *     @OA\Property(property="taken_by", type="string", enum={"self", "other"}, example="self"),
 *     @OA\Property(property="taken_by_name", type="string", nullable=true, example="John Doe"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2024-03-20T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2024-03-20T10:00:00Z"),
 *     @OA\Property(
 *         property="agreement",
 *         ref="#/components/schemas/AgreementResource"
 *     )
 * )
 */
class AdvancePaymentResource extends JsonResource
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
            'amount' => (float)$this->amount,
            'payment_date' => $this->payment_date->format('Y-m-d'),
            'taken_by' => $this->taken_by,
            'taken_by_name' => $this->taken_by_name,
            'farmer' => new FarmerResource($this->whenLoaded('farmer')),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
} 