<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

/**
 * @OA\Schema(
 *     schema="FarmerRequest",
 *     title="Farmer Request",
 *     description="Farmer request validation",
 *     required={"name", "contact_number", "village_name"},
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="contact_number", type="string", example="+1234567890"),
 *     @OA\Property(property="village_name", type="string", example="123 Main St")
 * )
 */
class FarmerRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Set to true to allow all users (adjust if needed)
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'required|digits:10',
            'village_name' => 'required|string|max:255',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all(); 
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => $errors[0], 
                'errors' => $validator->errors()
            ], 200)
        );
    }
}
