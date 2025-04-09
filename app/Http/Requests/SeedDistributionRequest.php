<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SeedDistributionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'seeds_booking_id' => ['required', 'integer', 'exists:seeds_bookings,id'],
            'farmer_id' => ['required', 'integer', 'exists:farmers,id'],
            'seed_variety_id' => ['required', 'integer', 'exists:seed_varieties,id'],
            'company_id' => ['required', 'integer', 'exists:companies,id'],
            'bag_quantity' => ['required', 'integer', 'min:1'],
            'distribution_date' => ['required', 'date'],
            'vehicle_number' => ['required', 'nullable', 'string', 'max:50'],
            'received_by' => ['required', 'nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'seeds_booking_id.required' => 'The seeds booking is required.',
            'seeds_booking_id.exists' => 'The selected seeds booking is invalid.',
            'farmer_id.required' => 'The farmer is required.',
            'farmer_id.exists' => 'The selected farmer is invalid.',
            'seed_variety_id.required' => 'The seed variety is required.',
            'seed_variety_id.exists' => 'The selected seed variety is invalid.',
            'company_id.required' => 'The company is required.',
            'company_id.exists' => 'The selected company is invalid.',
            'bag_quantity.required' => 'The bag quantity is required.',
            'bag_quantity.min' => 'The bag quantity must be at least 1.',
            'distribution_date.required' => 'The distribution date is required.',
            'distribution_date.date' => 'The distribution date must be a valid date.',
            'vehicle_number.max' => 'The vehicle number cannot exceed 50 characters.',
            'received_by.max' => 'The received by field cannot exceed 50 characters.',
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