<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Run daily at midnight
        $schedule->command('bookings:clean')
            ->daily()
            ->at('00:00')
            ->appendOutputTo(storage_path('logs/bookings-cleanup.log'));
    }
}