<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class BookingsTableSeeder extends Seeder
{
    public function run()
    {
        $services = Service::where('is_published', true)->get();
        $customers = User::where('role', 'customer')->get();

        foreach ($services as $service) {
            $bookingCount = rand(0, 15);

            for ($i = 0; $i < $bookingCount; $i++) {
                $startTime = Carbon::now()
                    ->addDays(rand(-30, 60))
                    ->setTime(rand(8, 16), fake()->randomElement([0, 15, 30, 45]));

                $status = fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']);

                // Ensure past bookings are completed
                if ($startTime->isPast()) {
                    $status = fake()->randomElement(['completed', 'cancelled']);
                }

                Booking::create([
                    'user_id' => $customers->random()->id,
                    'service_id' => $service->id,
                    'start_time' => $startTime,
                    'end_time' => $startTime->copy()->addMinutes($service->duration),
                    'status' => $status,
                ]);
            }
        }
    }
}
