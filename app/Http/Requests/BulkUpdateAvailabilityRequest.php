<?php
namespace App\Http\Requests;

use App\Enums\DayOfWeek;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkUpdateAvailabilityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->isProvider();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'availabilities' => ['required', 'array', 'min:1'],
            'availabilities.*.day_of_week' => [
                'required',
                Rule::enum(DayOfWeek::class)
            ],
            'availabilities.*.start_time' => [
                'required',
                'date_format:H:i',
                'before:availabilities.*.end_time'
            ],
            'availabilities.*.end_time' => [
                'required',
                'date_format:H:i',
                'after:availabilities.*.start_time'
            ],
            'availabilities.*.recurring' => [
                'sometimes',
                'boolean'
            ],
            'availabilities.*.start_date' => [
                'nullable',
                'date',
                'required_if:availabilities.*.recurring,false'
            ],
            'availabilities.*.end_date' => [
                'nullable',
                'date',
                'after_or_equal:availabilities.*.start_date',
                'required_if:availabilities.*.recurring,false'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'availabilities.*.day_of_week' => 'Invalid day of week',
            'availabilities.*.start_time.before' => 'Start time must be before end time',
            'availabilities.*.end_time.after' => 'End time must be after start time',
            'availabilities.*.start_date.required_if' => 'Start date is required for non-recurring availability',
            'availabilities.*.end_date.required_if' => 'End date is required for non-recurring availability'
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'availabilities' => array_map(function ($item) {
                return array_merge([
                    'recurring' => $item['recurring'] ?? true
                ], $item);
            }, $this->availabilities)
        ]);
    }
}
