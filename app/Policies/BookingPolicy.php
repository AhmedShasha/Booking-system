<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isProvider() || $user->isAdmin();
    }

    public function view(User $user, Booking $booking): bool
    {
        return $booking->service->provider_id === $user->id || $booking->user_id === $user->id || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isCustomer() || $user->isAdmin();
    }

    public function confirm(User $user, Booking $booking): bool
    {
        return $booking->service->provider_id === $user->id || $user->isAdmin();
    }

    public function cancel(User $user, Booking $booking): bool
    {
        return $booking->service->provider_id === $user->id || $booking->user->id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, Booking $booking): bool
    {
        // Only allow if no future bookings depend on this Booking
        $noFutureBookings = !$booking->where('start_time', '>', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        return ( $booking->user_id === $user->id || $booking->service->provider_id === $user->id && $noFutureBookings) || $user->isAdmin();
    }
}
