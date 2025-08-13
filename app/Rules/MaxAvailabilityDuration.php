<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxAvailabilityDuration implements ValidationRule
{
    private string $startTime;
    private int $maxHours;

    public function __construct(string $startTime, int $maxHours = 12)
    {
        $this->startTime = $startTime;
        $this->maxHours = $maxHours;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start = Carbon::parse($this->startTime);
        $end = Carbon::parse($value);
        
        if ($end->diffInHours($start) > $this->maxHours) {
            $fail("Availability slot cannot be longer than {$this->maxHours} hours.");
        }
    }
}