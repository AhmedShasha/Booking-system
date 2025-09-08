<?php

namespace Tests\Unit;

use App\Events\BookingCreated;
use App\Exceptions\BookingValidationException;
use App\Exceptions\TimeSlotNotAvailableException;
use App\Models\Service;
use App\Models\User;
use App\Models\Availability;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    use RefreshDatabase;

    private BookingService $bookingService;
    private User $customer;
    private User $provider;
    private Service $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->bookingService = app(BookingService::class);
        
        // Create test customer
        $this->customer = User::factory()->create([
            'role' => 'customer'
        ]);

        // Create test provider
        $this->provider = User::factory()->create([
            'role' => 'provider'
        ]);
        
        // Create test service
        $this->service = Service::factory()->create([
            'provider_id' => $this->provider->id,
            'is_published' => true,
            'duration' => 60
        ]);

        // Create availability for provider
        Availability::create([
            'user_id' => $this->provider->id,
            'service_id' => $this->service->id,
            'day_of_week' => strtolower(Carbon::now()->addDay()->format('l')),
            'start_time' => '09:00',
            'end_time' => '17:00',
            'is_available' => true,
            'recurring' => true
        ]);

        // Prevent event listeners from running
        Event::fake();
    }

    public function test_cannot_book_in_past(): void
    {
        $this->expectException(BookingValidationException::class);

        $pastDateTime = Carbon::now()->subHour();
        
        $this->bookingService->createBooking(
            start_time: $pastDateTime,
            service: $this->service,
            user: $this->customer
        );
    }

    public function test_cannot_double_book_slot(): void
    {
        $this->expectException(TimeSlotNotAvailableException::class);

        $startTime = Carbon::now()->addDay()->setHour(10)->setMinute(0);

        // Create first booking
        $this->bookingService->createBooking(
            start_time: $startTime,
            service: $this->service,
            user: $this->customer
        );

        // Try to create second booking for same time
        $this->bookingService->createBooking(
            start_time: $startTime,
            service: $this->service,
            user: $this->customer
        );
    }

    public function test_booking_creation_dispatches_event(): void
    {
        $startTime = Carbon::now()->addDay()->setHour(10)->setMinute(0);

        $booking = $this->bookingService->createBooking(
            start_time: $startTime,
            service: $this->service,
            user: $this->customer
        );

        Event::assertDispatched(BookingCreated::class, function ($event) use ($booking) {
            return $event->booking->id === $booking->id;
        });
    }
}