<?php

namespace App\Notifications;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingStatusUpdated extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private Booking $booking,
        private BookingStatus $oldStatus,
        private BookingStatus $newStatus
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Status Updated')
            ->line("Your booking for {$this->booking->service->name} has been updated.")
            ->line("Status changed from {$this->oldStatus->value} to {$this->newStatus->value}.")
            ->line("Date: {$this->booking->start_time->format('Y-m-d H:i')}")
            ->action('View Booking', url("/bookings/{$this->booking->id}"));
    }

    public function toArray($notifiable): array
    {
        return [
            'booking_id' => $this->booking->id,
            'old_status' => $this->oldStatus->value,
            'new_status' => $this->newStatus->value,
            'service_name' => $this->booking->service->name,
            'start_time' => $this->booking->start_time->toIso8601String()
        ];
    }
}