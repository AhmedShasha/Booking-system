<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    public function getBookingsReport(array $filters = [])
    {
        $query = Booking::with(['service', 'user'])
            ->when(isset($filters['provider_id']), function ($q) use ($filters) {
                $q->whereHas('service', function ($q) use ($filters) {
                    $q->where('provider_id', $filters['provider_id']);
                });
            })
            ->when(isset($filters['service_id']), function ($q) use ($filters) {
                $q->where('service_id', $filters['service_id']);
            })
            ->when(isset($filters['date_from']), function ($q) use ($filters) {
                $q->where('start_time', '>=', $filters['date_from']);
            })
            ->when(isset($filters['date_to']), function ($q) use ($filters) {
                $q->where('start_time', '<=', $filters['date_to']);
            });

        return $query->get();
    }

    public function getPeakHours(array $filters = [])
    {
        return DB::table('bookings')
            ->select(DB::raw('HOUR(start_time) as hour'), DB::raw('COUNT(*) as count'))
            ->where('status', BookingStatus::CONFIRMED->value)
            ->groupBy('hour')
            ->orderBy('count', 'desc')
            ->get();
    }
}
