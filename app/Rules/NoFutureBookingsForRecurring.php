<?php

namespace App\Rules;

use App\Models\Availability;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class NoFutureBookingsForRecurring implements ValidationRule
{
    private Availability $availability;

    public function __construct(Availability $availability)
    {
        $this->availability = $availability;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value !== $this->availability->recurring) {
            $hasFutureBookings = $this->availability->service->bookings()
                ->where('start_time', '>', now())
                ->exists();
            
            if ($hasFutureBookings) {
                $fail('Cannot change recurring status when there are future bookings.');
            }
        }
    }
}