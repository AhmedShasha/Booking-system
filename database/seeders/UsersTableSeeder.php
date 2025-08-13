<?php

namespace Database\Seeders;

use App\Enums\DayOfWeek;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Create admin
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'timezone' => 'UTC',
        ]);

        // Create providers
        User::factory(10)->create([
            'role' => 'provider',
            'timezone' => fake()->timezone,
        ]);

        // Create customers
        User::factory(50)->create([
            'role' => 'customer',
            'timezone' => fake()->timezone,
        ]);
    }
}