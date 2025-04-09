<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AgreementRequest extends FormRequest
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
            'seed_variety_id' => 'required|exists:seed_varieties,id',
            'rate_per_kg' => 'required|numeric|min:0',
            'agreement_date' => 'required|date',
            'vighas' => 'required|numeric|min:0',
            'bag_quantity' => 'required|integer|min:1',
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