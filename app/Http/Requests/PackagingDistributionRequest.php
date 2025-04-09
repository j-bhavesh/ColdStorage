<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class PackagingDistributionRequest extends FormRequest
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
        $rules = [
            'agreement_id' => ['required', 'exists:agreements,id'],
            'bag_quantity' => ['required', 'integer', 'min:1'],
            'vehicle_number' => ['required', 'string', 'max:50'],
            'distribution_date' => ['required', 'date'],
            'received_by' => ['required', 'string', 'max:50'],
        ];

        // If this is an update request, make all fields optional
        // if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
        //     $rules = array_map(function ($rule) {
        //         return array_merge(['sometimes'], $rule);
        //     }, $rules);
        // }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'agreement_id.required' => 'Please select a agreement.',
            'agreement_id.exists' => 'The selected agreement is invalid.',
            'bag_quantity.required' => 'Bag quantity is required.',
            'bag_quantity.integer' => 'Bag quantity must be a whole number.',
            'bag_quantity.min' => 'Bag quantity must be at least 1.',
            'vehicle_number.required' => 'Vehicle number is required.',
            'vehicle_number.max' => 'Vehicle number cannot exceed 50 characters.',
            'distribution_date.required' => 'Distribution date is required.',
            'distribution_date.date' => 'Please enter a valid date.',
            'received_by.required' => 'Receiver name is required.',
            'received_by.max' => 'Receiver name cannot exceed 50 characters.',
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