<?php

namespace Database\Seeders;

use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;

class ServicesTableSeeder extends Seeder
{
    public function run()
    {
        $categories = ['Cleaning', 'Repair', 'Beauty', 'Fitness', 'Consulting', 'Education'];
        $providers = User::where('role', 'provider')->get();

        foreach ($providers as $provider) {
            $serviceCount = rand(1, 5);

            for ($i = 0; $i < $serviceCount; $i++) {
                Service::create([
                    'provider_id' => $provider->id,
                    'name' => fake()->words(3, true),
                    'description' => fake()->paragraph,
                    'category' => fake()->randomElement($categories),
                    'duration' => fake()->randomElement([30, 45, 60, 90, 120]),
                    'price' => fake()->randomFloat(2, 10, 200),
                    'is_published' => fake()->boolean(90),
                ]);
            }
        }
    }
}
