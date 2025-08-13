<?php

namespace Database\Factories;

use App\Enums\BookingStatus;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $startTime = Carbon::instance(fake()->dateTimeBetween('+1 day', '+1 month'));

        return [
            'user_id' => User::factory()->create(['role' => 'customer'])->id,
            'service_id' => Service::factory(),
            'start_time' => $startTime,
            'end_time' => $startTime->copy()->addHour(),
            'status' => BookingStatus::PENDING
        ];
    }

    public function confirmed(): self
    {
        return $this->state(function (array $attributes) {
            return ['status' => BookingStatus::CONFIRMED];
        });
    }

    public function cancelled(): self
    {
        return $this->state(function (array $attributes) {
            return ['status' => BookingStatus::CANCELLED];
        });
    }
}
