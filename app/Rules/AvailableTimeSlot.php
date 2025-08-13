<?php

namespace App\Rules;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AvailableTimeSlot implements ValidationRule
{
    private Service $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $startTime = Carbon::parse($value);
        $endTime = $startTime->copy()->addMinutes($this->service->duration);

        $overlappingBooking = Booking::query()
            ->where('service_id', $this->service->id)
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime])
                    ->orWhere(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<=', $startTime)
                            ->where('end_time', '>=', $endTime);
                    });
            })
            ->whereIn('status', [
                BookingStatus::PENDING->value,
                BookingStatus::CONFIRMED->value
            ])
            ->exists();

        if ($overlappingBooking) {
            $fail('The selected time slot is already booked.');
        }
    }
}