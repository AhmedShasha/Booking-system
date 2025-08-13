<?php

namespace App\Rules;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidBookingStatusTransition implements ValidationRule
{
    private Booking $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (in_array($this->booking->status, [BookingStatus::CANCELLED, BookingStatus::COMPLETED])) {
            $fail("Cannot change status from {$this->booking->status->value}.");
            return;
        }

        if ($this->booking->status === BookingStatus::PENDING) {
            $allowedStatuses = [BookingStatus::CONFIRMED, BookingStatus::CANCELLED];
            if (!in_array(BookingStatus::from($value), $allowedStatuses)) {
                $fail('Pending bookings can only be confirmed or cancelled.');
            }
        }

        if ($this->booking->status === BookingStatus::CONFIRMED 
            && BookingStatus::from($value) !== BookingStatus::COMPLETED
        ) {
            $fail('Confirmed bookings can only be marked as completed.');
        }
    }
}