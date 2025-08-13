<?php
namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $booking;
    public $reason;

    public function __construct(Booking $booking, string $reason = null)
    {
        $this->booking = $booking;
        $this->reason = $reason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Booking Cancelled: ' . $this->booking->service->name)
            ->line('The following booking has been cancelled:')
            ->line('Customer: ' . $this->booking->user->name)
            ->line('Service: ' . $this->booking->service->name)
            ->line('Original Time: ' . $this->booking->start_time->format('l, F j, Y \a\t g:i A'));

        if ($this->reason) {
            $mail->line('Reason: ' . $this->reason);
        }

        return $mail;
    }

    public function toArray($notifiable)
    {
        return [
            'booking_id' => $this->booking->id,
            'service' => $this->booking->service->name,
            'customer' => $this->booking->user->name,
            'time' => $this->booking->start_time->toDateTimeString(),
            'reason' => $this->reason,
            'message' => 'Booking cancelled',
        ];
    }
}