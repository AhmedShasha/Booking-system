<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Notifications\NewBookingNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyProviderAboutNewBooking implements ShouldQueue
{
    public function handle(BookingCreated $event)
    {
        $event->booking->service->provider
            ->notify(new NewBookingNotification($event->booking));
    }
}
