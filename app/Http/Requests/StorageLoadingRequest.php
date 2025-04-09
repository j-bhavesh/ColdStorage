<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StorageLoadingRequest extends FormRequest
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
            'agreement_id' => 'required|exists:agreements,id',
            'transporter_id' => 'required|exists:transporters,id',
            // 'vehicle_id' => 'required|exists:vehicles,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'vehicle_number' => 'required|string|max:50',
            'cold_storage_id' => 'required|exists:cold_storages,id',
            'rst_number' => 'required|string|max:50',
            'chamber_no' => 'required|string|max:50',
            'bag_quantity' => 'required|numeric|min:1',
            'net_weight' => 'required|numeric|min:0',
            'extra_bags' => 'nullable|integer|min:0',
            'remarks' => 'nullable|string',
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
            'agreement_id.required' => 'The agreement is required.',
            'agreement_id.exists' => 'The selected agreement is invalid.',
            'transporter_id.required' => 'The transporter is required.',
            'transporter_id.exists' => 'The selected transporter is invalid.',
            'vehicle_id.required' => 'The vehicle is required.',
            'vehicle_id.exists' => 'The selected vehicle is invalid.',
            'cold_storage_id.required' => 'The cold storage is required.',
            'cold_storage_id.exists' => 'The selected cold storage is invalid.',
            'chamber_no.required' => 'Chamber number is required',
            'chamber_no.max' => 'Chamber number cannot exceed 50 characters',
            'bag_quantity.required' => 'The bag quantity is required.',
            'bag_quantity.numeric' => 'The bag quantity must be a numeric.',
            'bag_quantity.min' => 'The bag quantity must be greater than or equal to 1.',
            'net_weight.required' => 'The net weight is required.',
            'net_weight.numeric' => 'The net weight must be a number.',
            'net_weight.min' => 'The net weight must be greater than or equal to 1.',
            'extra_bags.numeric' => 'The extra bags must be a number.',
            'extra_bags.min' => 'The extra bags must be greater than or equal to 0.',
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