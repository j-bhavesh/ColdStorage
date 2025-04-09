<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VehicleRequest extends FormRequest
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
            'vehicle_number' => ['required', 'string', 'max:20', 'unique:vehicles,vehicle_number,' . $this->id],
            'transporter_id' => ['required', 'exists:transporters,id'],
            'vehicle_type' => ['required', 'string', 'max:50'],
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
            'vehicle_number.required' => 'Vehicle number is required.',
            'vehicle_number.unique' => 'This vehicle number is already registered.',
            'vehicle_number.max' => 'Vehicle number cannot exceed 20 characters.',
            'transporter_id.required' => 'Please select a transporter.',
            'transporter_id.exists' => 'The selected transporter is invalid.',
            'vehicle_type.required' => 'Vehicle type is required.',
            'vehicle_type.max' => 'Vehicle type cannot exceed 50 characters.',
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