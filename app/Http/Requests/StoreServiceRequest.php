<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isProvider();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'category' => ['required', 'string', 'max:255'],
            'duration' => ['required', 'integer', 'min:15', 'max:240'], // 15min to 4hrs
            'price' => ['required', 'numeric', 'min:0', 'max:9999.99'],
            'is_published' => ['sometimes', 'boolean']
        ];
    }

    public function messages(): array
    {
        return [
            'duration.min' => 'Duration must be at least 15 minutes',
            'duration.max' => 'Duration cannot exceed 4 hours',
            'price.max' => 'Price cannot exceed 9999.99'
        ];
    }
}