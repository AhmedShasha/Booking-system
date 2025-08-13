<?php 

namespace App\Http\Requests;

use App\Enums\DayOfWeek;
use App\Rules\MaxAvailabilityDuration;
use App\Rules\MaxAvailabilityRange;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->isProvider();
    }

    public function rules(): array
    {
        return [
            'day_of_week' => [
                'required',
                Rule::in(DayOfWeek::values())
            ],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                new MaxAvailabilityDuration($this->start_time)
            ],
            'recurring' => ['sometimes', 'boolean'],
            'start_date' => ['nullable', 'date', 'after_or_equal:today'],
            'end_date' => [
                'nullable',
                'date',
                'after:start_date',
                new MaxAvailabilityRange($this->start_date)
            ]
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'recurring' => $this->recurring ?? true,
            'day_of_week' => strtolower($this->day_of_week)
        ]);
    }
}