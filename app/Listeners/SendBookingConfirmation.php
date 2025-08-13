<?php

namespace App\Listeners;

use App\Events\BookingCreated;
use App\Mail\BookingConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmation implements ShouldQueue
{
    public function handle(BookingCreated $event)
    {
        Mail::to($event->booking->user->email)
            ->send(new BookingConfirmation($event->booking));
    }
}
