<?php

namespace App\Rules;

use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MaxAvailabilityRange implements ValidationRule
{
    private ?string $startDate;
    private int $maxMonths;

    public function __construct(?string $startDate, int $maxMonths = 6)
    {
        $this->startDate = $startDate;
        $this->maxMonths = $maxMonths;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->startDate && $value) {
            $start = Carbon::parse($this->startDate);
            $end = Carbon::parse($value);
            
            if ($end->diffInMonths($start) > $this->maxMonths) {
                $fail("Availability cannot be set for more than {$this->maxMonths} months in advance.");
            }
        }
    }
}