<?php

namespace Tests\Unit;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Service;
use App\Services\ReportingService;
use Tests\TestCase;

class ReportingServiceTest extends TestCase
{
    private ReportingService $reportingService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportingService = new ReportingService();
    }

    public function test_can_get_bookings_by_provider()
    {
        $provider = $this->createProvider();
        $service = Service::factory()->create(['provider_id' => $provider->id]);
        Booking::factory()->count(3)->create([
            'service_id' => $service->id,
            'status' => BookingStatus::CONFIRMED
        ]);

        $report = $this->reportingService->getBookingsReport([
            'provider_id' => $provider->id
        ]);

        $this->assertCount(3, $report);
    }
}