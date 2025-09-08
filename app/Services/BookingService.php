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
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;
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

    public function getUserBookings($user)
    {
        if ($user->isProvider()) {
            return Booking::whereHas('service', function ($query) use ($user) {
                $query->where('provider_id', $user->id);
            })->with(['service', 'user'])->get();
        } else {
            return Booking::where('user_id', $user->id)
                ->with(['service', 'service.provider'])
                ->get();
        }
    }

    public function createBooking($start_time, $service, $user): Booking
    {
        return DB::transaction(function () use ($start_time, $service, $user) {
            
            if (!$service->is_published) {
                throw new BookingValidationException('Service is not available for booking');
            }

            $startTime = Carbon::parse($start_time)->setTimezone($service->provider->timezone);
            $endTime = $startTime->copy()->addMinutes($service->duration);

            $this->validateTimeSlot($service, $startTime, $endTime);

            $booking = Booking::create([
                'user_id' => $user->id,
                'service_id' => $service->id,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => BookingStatus::PENDING
            ]);

            event(new BookingCreated($booking));

            return $booking;
        });
    }

    private function validateTimeSlot(Service $service, Carbon $startTime, Carbon $endTime): void
    {
        if ($startTime->isPast()) {
            throw new BookingValidationException('Cannot book in the past');
        }

        $isAvailable = $service->availabilities()
            ->where(function ($query) use ($startTime) {
                $query->where('day_of_week', strtolower($startTime->format('l')))
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
