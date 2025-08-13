<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

class AvailabilityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'day_of_week' => fake()->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday']),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'recurring' => true,
            'override_date' => null
        ];
    }

    public function nonRecurring(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'recurring' => false,
                'override_date' => fake()->dateTimeBetween('now', '+1 month')->format('Y-m-d')
            ];
        });
    }
}