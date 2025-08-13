<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Seeder;

class NotificationsTableSeeder extends Seeder
{
    public function run()
    {
        $bookings = Booking::all();

        foreach ($bookings as $booking) {
            // Create notification for the booking
            $booking->user->notifications()->create([
                'type' => 'App\Notifications\BookingConfirmation',
                'data' => json_encode([
                    'booking_id' => $booking->id,
                    'service' => $booking->service->name,
                    'time' => $booking->start_time->toDateTimeString(),
                    'message' => 'Your booking has been confirmed',
                ]),
                'read_at' => fake()->optional(0.7)->dateTime, // 70% chance of being read
            ]);

            // Create notification for the provider
            $booking->service->provider->notifications()->create([
                'type' => 'App\Notifications\NewBookingNotification',
                'data' => json_encode([
                    'booking_id' => $booking->id,
                    'customer' => $booking->user->name,
                    'service' => $booking->service->name,
                    'time' => $booking->start_time->toDateTimeString(),
                    'message' => 'New booking received',
                ]),
                'read_at' => fake()->optional(0.5)->dateTime, // 50% chance of being read
            ]);
        }
    }
}
