<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Events\BookingCreated;
use App\Events\BookingStatusChanged;
use App\Exceptions\BookingValidationException;
use App\Exceptions\TimeSlotNotAvailableException;
use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookingService
{
    private array $validTransitions = [
        BookingStatus::PENDING->value => [
            BookingStatus::CONFIRMED,
            BookingStatus::CANCELLED
        ],
        BookingStatus::CONFIRMED->value => [
            BookingStatus::COMPLETED,
            BookingStatus::CANCELLED
        ]
    ];

    public function createBooking(array $data): Booking
    {
        return DB::transaction(function () use ($data) {
            $service = Service::findOrFail($data['service_id']);
            
            if (!$service->is_published) {
                throw new BookingValidationException('Service is not available for booking');
            }

            $providerTimezone = $service->provider->getTimezone();
            
            $startTime = Carbon::parse($data['start_time'])
                ->setTimezone($providerTimezone)
                ->startOfMinute();
                
            $endTime = $startTime->copy()
                ->addMinutes($service->duration)
                ->startOfMinute();

            $this->validateTimeSlot($service, $startTime, $endTime);

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'service_id' => $service->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => BookingStatus::PENDING
            ]);

            event(new BookingCreated($booking));

            return $booking;
        });
    }

    public function updateStatus(Booking $booking, BookingStatus $newStatus): Booking
    {
        if (!$this->isValidTransition($booking->status, $newStatus)) {
            throw new BookingValidationException(
                "Invalid status transition from {$booking->status->value} to {$newStatus->value}"
            );
        }

        $oldStatus = $booking->status;
        $booking->update(['status' => $newStatus]);

        event(new BookingStatusChanged($booking, $oldStatus, $newStatus));

        return $booking;
    }

    private function isValidTransition(BookingStatus $currentStatus, BookingStatus $newStatus): bool
    {
        return in_array($newStatus, $this->validTransitions[$currentStatus->value] ?? []);
    }

    private function validateTimeSlot(Service $service, Carbon $startTime, Carbon $endTime): void
    {
        if ($startTime->isPast()) {
            throw new BookingValidationException('Cannot book in the past');
        }

        $dayOfWeek = strtolower($startTime->format('l'));

        $isAvailable = $service->availabilities()
            ->where(function ($query) use ($dayOfWeek, $startTime) {
                $query->where('day_of_week', $dayOfWeek)
                    ->orWhere('override_date', $startTime->toDateString());
            })
            ->exists();

        if (!$isAvailable) {
            throw new TimeSlotNotAvailableException('Provider is not available at this time');
        }

        $overlappingBooking = $service->bookings()
            ->where(function ($query) use ($startTime, $endTime) {
                $query->whereBetween('start_time', [$startTime, $endTime])
                    ->orWhereBetween('end_time', [$startTime, $endTime]);
            })
            ->whereIn('status', [BookingStatus::PENDING->value, BookingStatus::CONFIRMED->value])
            ->exists();

        if ($overlappingBooking) {
            throw new TimeSlotNotAvailableException('Time slot is already booked');
        }
    }

    public function getAvailableSlots(Service $service, string $date): array
    {
        try {
            $providerTimezone = $service->provider->getTimezone();
            $dateCarbon = Carbon::parse($date)
                ->setTimezone($providerTimezone)
                ->startOfDay();
            $dayOfWeek = strtolower($dateCarbon->englishDayOfWeek);

            // Get service availability for the day
            $availability = $service->availabilities()
                ->where(function ($query) use ($dayOfWeek, $dateCarbon) {
                    $query->where('day_of_week', $dayOfWeek)
                        ->orWhere('override_date', $dateCarbon->toDateString());
                })
                ->first();

            if (!$availability) {
                return [];
            }

            // Convert times to provider's timezone
            $startTime = Carbon::parse($availability->start_time, $providerTimezone);
            $endTime = Carbon::parse($availability->end_time, $providerTimezone);
            
            $slots = [];
            $interval = CarbonInterval::minutes($service->duration);
            
            $period = new CarbonPeriod($startTime, $interval, $endTime->subMinutes($service->duration));

            // Get existing bookings in provider's timezone
            $existingBookings = $service->bookings()
                ->whereDate('start_time', $dateCarbon)
                ->whereIn('status', [BookingStatus::PENDING->value, BookingStatus::CONFIRMED->value])
                ->get()
                ->map(function ($booking) use ($providerTimezone) {
                    $booking->start_time = Carbon::parse($booking->start_time)->setTimezone($providerTimezone);
                    $booking->end_time = Carbon::parse($booking->end_time)->setTimezone($providerTimezone);
                    return $booking;
                });

            // Filter available slots
            foreach ($period as $slotStart) {
                $slotEnd = $slotStart->copy()->addMinutes($service->duration);
                
                $isAvailable = !$existingBookings->contains(function ($booking) use ($slotStart, $slotEnd) {
                    return $slotStart->between($booking->start_time, $booking->end_time) ||
                        $slotEnd->between($booking->start_time, $booking->end_time) ||
                        ($slotStart->lte($booking->start_time) && $slotEnd->gte($booking->end_time));
                });

                if ($isAvailable && $slotStart->isFuture()) {
                    $slots[] = [
                        'start_time' => $slotStart->toIso8601String(),
                        'end_time' => $slotEnd->toIso8601String(),
                    ];
                }
            }

            return $slots;
        } catch (\Exception $e) {
            throw new BookingValidationException('Error getting available slots: ' . $e->getMessage());
        }
    }
}
