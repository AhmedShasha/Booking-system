<?php

namespace Database\Factories;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'role' => RoleEnum::CUSTOMER, // Default role
            'timezone' => $this->faker->timezone(),
        ];
    }

    public function admin()
    {
        return $this->state([
            'role' => RoleEnum::ADMIN,
        ]);
    }

    public function provider()
    {
        return $this->state([
            'role' => RoleEnum::PROVIDER,
        ]);
    }

    public function customer()
    {
        return $this->state([
            'role' => RoleEnum::CUSTOMER,
        ]);
    }
}
