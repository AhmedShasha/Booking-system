<?php

namespace App\Listeners;

use App\Enums\BookingStatus;
use App\Events\BookingStatusChanged;
use App\Notifications\BookingStatusUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendBookingStatusNotification implements ShouldQueue
{
    public function handle(BookingStatusChanged $event): void
    {
        // Notify customer
        $event->booking->user->notify(new BookingStatusUpdated(
            $event->booking,
            $event->oldStatus,
            $event->newStatus
        ));

        // Notify provider if relevant status change
        if ($this->shouldNotifyProvider($event)) {
            $event->booking->service->provider->notify(new BookingStatusUpdated(
                $event->booking,
                $event->oldStatus,
                $event->newStatus
            ));
        }
    }

    private function shouldNotifyProvider(BookingStatusChanged $event): bool
    {
        return in_array($event->newStatus, [
            BookingStatus::PENDING,
            BookingStatus::CANCELLED
        ]);
    }
}