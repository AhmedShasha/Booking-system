<?php
namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingCancelledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $reason;

    public function __construct(Booking $booking, string $reason = null)
    {
        $this->booking = $booking;
        $this->reason = $reason;
    }

    public function build()
    {
        return $this->markdown('emails.bookings.cancelled')
            ->subject('Booking Cancellation: ' . $this->booking->service->name);
    }
}