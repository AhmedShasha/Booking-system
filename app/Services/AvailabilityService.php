<?php

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Models\Availability;
use App\Models\Service;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AvailabilityService
{
    public function getAvailableSlots(Service $service, Carbon $startDate, Carbon $endDate): array
    {
        $slots = [];
        $duration = $service->duration;
        $period = CarbonPeriod::create($startDate, $endDate);

        foreach ($period as $date) {
            $dayOfWeek = strtolower($date->englishDayOfWeek);
            $availabilities = $service->availabilities()
                ->where(function ($query) use ($dayOfWeek, $date) {
                    $query->where('day_of_week', $dayOfWeek)
                        ->orWhere('override_date', $date->toDateString());
                })
                ->get();

            foreach ($availabilities as $availability) {
                $start = Carbon::parse($date->toDateString() . ' ' . $availability->start_time);
                $end = Carbon::parse($date->toDateString() . ' ' . $availability->end_time);

                $current = $start->copy();
                while ($current->addMinutes($duration)->lte($end)) {
                    $slotEnd = $current->copy();
                    if ($this->isSlotAvailable($service, $current, $slotEnd)) {
                        $slots[] = [
                            'start_time' => $current->toDateTimeString(),
                            'end_time' => $slotEnd->toDateTimeString(),
                        ];
                    }
                    $current = $slotEnd;
                }
            }
        }

        return $slots;
    }

    private function isSlotAvailable(Service $service, Carbon $start, Carbon $end): bool
    {
        if ($start->isPast()) {
            return false;
        }

        return !$service->bookings()
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('start_time', [$start, $end])
                    ->orWhereBetween('end_time', [$start, $end])
                    ->orWhere(function ($query) use ($start, $end) {
                        $query->where('start_time', '<', $start)
                            ->where('end_time', '>', $end);
                    });
            })
            ->whereNotIn('status', ['cancelled'])
            ->exists();
    }

    /**
     * Check if the availability has any future bookings
     */
    public function hasFutureBookings(Availability $availability): bool
    {
        $provider = $availability->provider;

        return $provider->bookings()
            ->where(function ($query) use ($availability) {
                // For recurring availability, check bookings on the same day of week
                if ($availability->recurring) {
                    $query->whereRaw("DAYOFWEEK(start_time) = ?", [
                        $this->getDayOfWeekIndex($availability->day_of_week)
                    ]);
                }
                // For one-time availability, check bookings on the specific date
                else {
                    $query->whereDate('start_time', '>=', $availability->start_date)
                        ->whereDate('start_time', '<=', $availability->end_date);
                }
            })
            ->where('start_time', '>', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
    }

    /**
     * Convert day name to day of week index (1-7) using the DayOfWeek enum
     */
    private function getDayOfWeekIndex(string $dayName): int
    {
        $dayOfWeek = DayOfWeek::from(strtolower($dayName));

        return match ($dayOfWeek) {
            DayOfWeek::SUNDAY => 1,
            DayOfWeek::MONDAY => 2,
            DayOfWeek::TUESDAY => 3,
            DayOfWeek::WEDNESDAY => 4,
            DayOfWeek::THURSDAY => 5,
            DayOfWeek::FRIDAY => 6,
            DayOfWeek::SATURDAY => 7,
        };
    }
}
