<?php

namespace App\Rules;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class BookingTimeSlotAvailable implements ValidationRule
{
    private Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = $this->booking->service;
        $startTime = Carbon::parse($value);
        $endTime = $startTime->copy()->addMinutes($service->duration);

        $overlapping = $service->bookings()
            ->where('id', '!=', $this->booking->id)
            ->where(function($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->whereIn('status', [
                BookingStatus::PENDING->value,
                BookingStatus::CONFIRMED->value
            ])
            ->exists();

        if ($overlapping) {
            $fail('The selected time slot is already booked.');
        }
    }
}