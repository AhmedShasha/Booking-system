<?php

namespace Database\Seeders;

namespace Database\Seeders;

use App\Enums\DayOfWeek;
use App\Enums\RoleEnum;
use App\Models\Availability;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class AvailabilitiesTableSeeder extends Seeder
{
    public function run()
    {
        $services = Service::all();

        foreach ($services as $service) {
            // Create recurring weekly availability
            foreach (DayOfWeek::cases() as $day) {
                if (fake()->boolean(70)) { // 70% chance of being available each day
                    Availability::create([
                        'service_id' => $service->id,
                        'day_of_week' => $day->value,
                        'start_time' => fake()->time('H:i', '08:00'),
                        'end_time' => fake()->time('H:i', '17:00'),
                        'recurring' => true,
                    ]);
                }
            }

            // Create some specific date overrides
            for ($i = 0; $i < 5; $i++) {
                Availability::create([
                    'service_id' => $service->id,
                    'day_of_week' => fake()->randomElement(DayOfWeek::cases())->value,
                    'start_time' => fake()->time('H:i', '08:00'),
                    'end_time' => fake()->time('H:i', '17:00'),
                    'recurring' => false,
                ]);
            }
        }
    }
}
