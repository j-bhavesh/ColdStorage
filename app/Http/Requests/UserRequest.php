<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'role' => ['required', 'exists:roles,name'],
        ];

        // Add password validation only for create or if password is provided
        if ($this->isMethod('POST') || $this->filled('password')) {
            $rules['password'] = ['required', Password::defaults()];
        }

        // Add unique email validation
        $rules['email'][] = 'unique:users,email,' . ($this->user ? $this->user->id : '');

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
            'name.required' => 'The name field is required.',
            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already taken.',
            'phone.required' => 'The phone field is required.',
            'password.required' => 'The password field is required.',
            'role.required' => 'Please select a role.',
            'role.exists' => 'The selected role is invalid.',
        ];
    }
} 