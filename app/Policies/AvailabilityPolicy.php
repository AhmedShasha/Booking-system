<?php

namespace App\Policies;

use App\Models\Availability;
use App\Models\User;

class AvailabilityPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isProvider() || $user->isAdmin();
    }

    public function view(User $user, Availability $availability): bool
    {
        return $availability->service->provider_id === $user->id || $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isProvider() || $user->isAdmin();
    }

    public function update(User $user, Availability $availability): bool
    {
        return $availability->service->provider_id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, Availability $availability): bool
    {
        // Only allow if no future bookings depend on this availability
        $noFutureBookings = !$availability->service->provider->bookings()
            ->where('start_time', '>', now())
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();

        return ($availability->service->provider_id === $user->id && $noFutureBookings) || $user->isAdmin();
    }

    public function bulkUpdate(User $user): bool
    {
        return $user->isProvider() || $user->isAdmin();
    }
}
