<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed'
            ],
            'timezone' => [
                'sometimes',
                'string',
                'timezone'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your name',
            'name.max' => 'Name cannot be longer than 255 characters',
            'email.required' => 'Please enter your email address',
            'email.email' => 'Please enter a valid email address',
            'email.unique' => 'This email is already registered',
            'password.required' => 'Please enter a password',
            'password.confirmed' => 'Password confirmation does not match',
            'role.in' => 'Invalid role selected',
            'timezone.timezone' => 'Invalid timezone'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'timezone' => $this->timezone ?? config('app.timezone')
        ]);
    }
}