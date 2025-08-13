<?php

namespace App\Policies;

use App\Models\Service;
use App\Models\User;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view published services
    }

    public function view(User $user, Service $service): bool
    {
        // Customers can only view published services
        if ($user->isCustomer()) {
            return $service->is_published;
        }
        
        // Providers can view their own services
        if ($user->isProvider()) {
            return $service->provider_id === $user->id;
        }
        
        // Admins can view all services
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isProvider() || $user->isAdmin();
    }

    public function update(User $user, Service $service): bool
    {
        return $service->provider_id === $user->id || $user->isAdmin();
    }

    public function delete(User $user, Service $service): bool
    {
        // Only allow if no future bookings exist
        $noFutureBookings = !$service->bookings()
            ->where('start_time', '>', now())
            ->exists();
            
        return ($service->provider_id === $user->id && $noFutureBookings) || $user->isAdmin();
    }

    public function publish(User $user, Service $service): bool
    {
        return $service->provider_id === $user->id || $user->isAdmin();
    }
}