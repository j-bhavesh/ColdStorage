<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class SeedsBookingRequest extends FormRequest
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
        // return [
        //     'farmer_id' => 'required|exists:farmers,id',
        //     'company_id' => 'required|exists:companies,id',
        //     'seed_variety_id' => 'required|exists:seed_varieties,id',
        //     'booking_amount' => 'required|numeric|min:0',
        //     'booking_type' => 'required|in:debit,cash',
        //     'bag_quantity' => 'required|integer|min:1',
        //     'bag_rate' => 'required|numeric|min:1',
        //     'status' => 'required|in:active,completed,rejected,hold',
        // ];

        $rules = [
            'farmer_id' => 'required|exists:farmers,id',
            'company_id' => 'required|exists:companies,id',
            'seed_variety_id' => 'required|exists:seed_varieties,id',
            'booking_type' => 'required|in:debit,cash',
            'bag_quantity' => 'required|integer|min:1',
            'bag_rate' => 'required|numeric|min:1',
            'status' => 'required|in:active,completed,rejected,hold',
        ];

        // Handle booking_amount differently for API vs Web
        if ($this->expectsJson() || $this->is('api/*')) {
            $rules['booking_amount'] = 'nullable|numeric|min:1|required_if:booking_type,cash';
        } else {
            //$rules['booking_amount'] = 'nullable|numeric|min:1|required_if:booking_type,cash';
            $rules['booking_amount'] = 'nullable|required_if:booking_type,cash|numeric|min:0';
            // if ($this->input('booking_type') === 'cash') {
            //     $rules['booking_amount'] = 'min:1';
            // }
        }

        return $rules;
    }

    // public function withValidator($validator)
    // {
    //     $validator->sometimes('booking_amount', 'required|numeric|min:1', function ($input) {
    //         return $input->booking_type === 'cash';
    //     });

    //     $validator->sometimes('booking_amount', 'nullable|numeric|min:0', function ($input) {
    //         return $input->booking_type === 'debit';
    //     });
    // }

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
