<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Mail\BookingCancelledMail;
use App\Notifications\BookingCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class ProcessBookingCancellation implements ShouldQueue
{
    public function handle(BookingCancelled $event)
    {
        // Send email to customer
        Mail::to($event->booking->user->email)
            ->send(new BookingCancelledMail(
                $event->booking,
                $event->cancellationReason
            ));

        // Send notification to provider
        $event->booking->service->provider
            ->notify(new BookingCancelledNotification(
                $event->booking,
                $event->cancellationReason
            ));
    }
}
