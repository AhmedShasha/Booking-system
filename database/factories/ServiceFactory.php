<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'description' => fake()->paragraph(),
            'category' => fake()->word(),
            'duration' => fake()->randomElement([30, 60, 90, 120]),
            'price' => fake()->randomFloat(2, 10, 200),
            'is_published' => true,
            'provider_id' => User::factory()->create(['role' => 'provider'])->id
        ];
    }

    public function unpublished(): self
    {
        return $this->state(function (array $attributes) {
            return ['is_published' => false];
        });
    }
}