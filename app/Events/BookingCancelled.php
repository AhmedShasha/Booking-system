<?php

namespace App\Events;

use App\Models\Booking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookingCancelled
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $booking;
    public $cancellationReason;

    public function __construct(Booking $booking, string $cancellationReason = null)
    {
        $this->booking = $booking;
        $this->cancellationReason = $cancellationReason;
    }

}
