<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ColdStorageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'capacity' => 'required|string|max:255',
            'remarks' => 'nullable|string',
        ];
    }
} 