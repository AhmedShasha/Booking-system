<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Service;

class UpdateServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $service = $this->route('service');
        return $service && $this->user()->can('update', $service);
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:1000'],
            'category' => ['sometimes', 'string', 'max:255'],
            'duration' => ['sometimes', 'integer', 'min:15', 'max:240'],
            'price' => ['sometimes', 'numeric', 'min:0', 'max:9999.99'],
            'is_published' => ['sometimes', 'boolean']
        ];
    }
}
