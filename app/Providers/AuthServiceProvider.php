<?php

namespace App\Providers;

use App\Enums\DayOfWeek;
use App\Models\Availability;
use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use App\Policies\AvailabilityPolicy;
use App\Policies\BookingPolicy;
use App\Policies\ServicePolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Service::class => ServicePolicy::class,
        Availability::class => AvailabilityPolicy::class,
        Booking::class => BookingPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define additional gates if needed
        Gate::define('view-dashboard', function (User $user) {
            return $user->isAdmin() || $user->isProvider();
        });

        // Or using enums for more complex rules
        Gate::define('manage-schedule', function (User $user, DayOfWeek $day) {
            return $user->isProvider() && $day !== DayOfWeek::SUNDAY;
        });
    }
}
