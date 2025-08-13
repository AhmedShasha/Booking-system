<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Mail\BookingConfirmedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendBookingConfirmedNotification implements ShouldQueue
{
    public function handle(BookingConfirmed $event)
    {
        Mail::to($event->booking->user->email)
            ->send(new BookingConfirmedMail($event->booking));
    }
}
