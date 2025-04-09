<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorageUnloadingRequest extends FormRequest
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
            'company_id' => ['required', 'exists:unloading_companies,id'],
            'cold_storage_id' => ['required', 'exists:cold_storages,id'],
            'transporter_id' => ['required', 'exists:transporters,id'],
            // 'vehicle_id' => ['required', 'exists:vehicles,id'],
            'vehicle_id' => ['nullable', 'exists:vehicles,id'],
            'vehicle_number' => ['required', 'string', 'max:50'],
            'seed_variety_id' => ['required', 'exists:seed_varieties,id'],
            'rst_no' => ['required', 'string', 'max:50'],
            'chamber_no' => ['required', 'string', 'max:50'],
            'location' => ['required', 'string', 'max:50'],
            'bag_quantity' => ['required', 'integer', 'min:1'],
            'weight' => ['required', 'numeric', 'min:0', 'decimal:0,2']
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
            'company_id.required' => 'Please select a company',
            'company_id.exists' => 'The selected company is invalid',
            'cold_storage_id.required' => 'Please select a cold storage',
            'cold_storage_id.exists' => 'The selected cold storage is invalid',
            'transporter_id.required' => 'Please select a transporter',
            'transporter_id.exists' => 'The selected transporter is invalid',
            'vehicle_id.required' => 'Please select a vehicle',
            'vehicle_id.exists' => 'The selected vehicle is invalid',
            'seed_variety_id.required' => 'Please select a seed variety',
            'seed_variety_id.exists' => 'The selected seed variety is invalid',
            'rst_no.required' => 'RST number is required',
            'rst_no.max' => 'RST number cannot exceed 50 characters',
            'chamber_no.required' => 'Chamber number is required',
            'chamber_no.max' => 'Chamber number cannot exceed 50 characters',
            'location.required' => 'Location is required',
            'location.max' => 'Location cannot exceed 50 characters',
            'bag_quantity.required' => 'Bag quantity is required',
            'bag_quantity.integer' => 'Bag quantity must be a whole number',
            'bag_quantity.min' => 'Bag quantity must be at least 1',
            'weight.required' => 'Weight is required',
            'weight.numeric' => 'Weight must be a number',
            'weight.min' => 'Weight cannot be negative',
            'weight.decimal' => 'Weight must have up to 2 decimal places'
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
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