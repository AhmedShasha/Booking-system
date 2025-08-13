<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CleanBookingsCommand extends Command
{
    protected $signature = 'bookings:clean';
    protected $description = 'Clean expired and unconfirmed bookings';

    public function handle(): int
    {
        $this->info('Starting booking cleanup...');

        // Clean unconfirmed bookings older than 24 hours
        $unconfirmedCount = Booking::where('status', BookingStatus::PENDING)
            ->where('created_at', '<', Carbon::now()->subHours(24))
            ->delete();

        $this->info("Deleted {$unconfirmedCount} unconfirmed bookings.");

        // Clean expired bookings (past bookings that were never marked as completed)
        $expiredCount = Booking::whereIn('status', [BookingStatus::PENDING, BookingStatus::CONFIRMED])
            ->where('end_time', '<', Carbon::now())
            ->delete();

        $this->info("Deleted {$expiredCount} expired bookings.");

        return Command::SUCCESS;
    }
}