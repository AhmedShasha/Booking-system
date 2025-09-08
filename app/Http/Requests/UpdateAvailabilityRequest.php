<?php

namespace App\Http\Requests;

use App\Enums\DayOfWeek;
use App\Rules\MaxAvailabilityDuration;
use App\Rules\MaxAvailabilityRange;
use App\Rules\NoFutureBookingsForRecurring;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        $availability = $this->route('availability');
        return $availability && $this->user()->can('update', $availability);
    }

    public function rules(): array
    {
        return [
            'day_of_week' => [
                'sometimes',
                Rule::in(DayOfWeek::values())
            ],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => [
                'sometimes',
                'date_format:H:i',
                new MaxAvailabilityDuration($this->start_time ?? '')
            ],
            'recurring' => [
                'sometimes', 
                'boolean',
                new NoFutureBookingsForRecurring($this->route('availability'))
            ],
            'start_date' => ['nullable', 'date'],
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
        if ($this->has('day_of_week')) {
            $this->merge([
                'day_of_week' => strtolower($this->day_of_week)
            ]);
        }
    }
}