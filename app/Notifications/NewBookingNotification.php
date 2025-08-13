<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBookingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Booking: ' . $this->booking->service->name)
            ->line('You have received a new booking from ' . $this->booking->user->name)
            ->line('Service: ' . $this->booking->service->name)
            ->line('Date: ' . $this->booking->start_time->format('l, F j, Y'))
            ->line('Time: ' . $this->booking->start_time->format('g:i A'))
            ->action('View Booking', url('/provider/bookings/' . $this->booking->id));
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'service' => $this->booking->service->name,
            'customer' => $this->booking->user->name,
            'time' => $this->booking->start_time->toDateTimeString(),
            'message' => 'New booking received',
        ];
    }
}
