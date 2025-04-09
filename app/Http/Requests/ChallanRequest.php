<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class ChallanRequest extends FormRequest
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
            'farmer_id' => 'required|exists:farmers,id',
            // 'vehicle_id' => 'required|exists:vehicles,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'vehicle_number' => 'required|string|max:50',
            'challan_number' => 'required|string|max:50|unique:challans,challan_number,' . ($this->challan ?? ''),
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
            'farmer_id.required' => 'The farmer is required.',
            'farmer_id.exists' => 'The selected farmer is invalid.',
            'vehicle_id.required' => 'The vehicle is required.',
            'vehicle_id.exists' => 'The selected vehicle is invalid.',
            'challan_number.required' => 'The challan number is required.',
            'challan_number.string' => 'The challan number must be a string.',
            'challan_number.max' => 'The challan number cannot exceed 50 characters.',
            'challan_number.unique' => 'This challan number is already in use.',
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