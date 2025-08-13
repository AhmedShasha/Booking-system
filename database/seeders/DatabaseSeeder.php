<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UsersTableSeeder::class,
            ServicesTableSeeder::class,
            AvailabilitiesTableSeeder::class,
            BookingsTableSeeder::class,
            NotificationsTableSeeder::class,
        ]);
    }
}
