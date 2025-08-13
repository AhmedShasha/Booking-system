<?php

namespace App\Providers;

use App\Events\BookingCreated;
use App\Events\BookingConfirmed;
use App\Events\BookingCancelled;
use App\Events\BookingRescheduled;
use App\Events\BookingStatusChanged;
use App\Listeners\SendBookingConfirmation;
use App\Listeners\NotifyProviderAboutNewBooking;
use App\Listeners\SendBookingConfirmedNotification;
use App\Listeners\ProcessBookingCancellation;
use App\Listeners\SendBookingStatusNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        BookingCreated::class => [
            SendBookingConfirmation::class,
            NotifyProviderAboutNewBooking::class,
        ],
        BookingConfirmed::class => [
            SendBookingConfirmedNotification::class,
        ],
        BookingCancelled::class => [
            ProcessBookingCancellation::class,
        ],
        BookingRescheduled::class => [
            // Add listeners for rescheduling if needed
        ],
        BookingStatusChanged::class => [
            SendBookingStatusNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}
