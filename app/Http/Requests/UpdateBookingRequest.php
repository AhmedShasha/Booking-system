<?php

namespace App\Http\Requests;

use App\Enums\BookingStatus;
use App\Rules\BookingTimeSlotAvailable;
use App\Rules\ValidBookingStatusTransition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $booking = $this->route('booking');
        return $booking && $this->user()->can('update', $booking);
    }

    public function rules(): array
    {
        $booking = $this->route('booking');
        
        return [
            'status' => [
                'sometimes',
                'string',
                Rule::in(BookingStatus::values()),
                new ValidBookingStatusTransition($booking)
            ],
            'start_time' => [
                'sometimes',
                'date',
                'after:now',
                new BookingTimeSlotAvailable($booking)
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Invalid booking status.',
            'start_time.after' => 'Booking time must be in the future.',
            'start_time.date' => 'Invalid date format.'
        ];
    }
}