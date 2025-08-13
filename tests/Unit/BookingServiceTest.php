<?php

namespace Tests\Unit;

use App\Enums\BookingStatus;
use App\Events\BookingCreated;
use App\Exceptions\BookingValidationException;
use App\Exceptions\TimeSlotNotAvailableException;
use App\Models\Availability;
use App\Models\Service;
use App\Models\User;
use App\Services\BookingService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BookingServiceTest extends TestCase
{
    private BookingService $bookingService;
    private Service $service;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Mock mail sending
        Mail::fake();
        
        // Mock events
        Event::fake();
        
        $this->bookingService = new BookingService();
        
        $this->user = User::factory()->create(['role' => 'customer']);
        $this->actingAs($this->user);

        // Create a service with availability
        $this->service = Service::factory()->create([
            'duration' => 60,
            'is_published' => true
        ]);

        // Create availability for tomorrow
        $dayOfWeek = strtolower(Carbon::tomorrow()->format('l'));
        Availability::factory()->create([
            'service_id' => $this->service->id,
            'day_of_week' => $dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'recurring' => true
        ]);
    }

    public function test_cannot_book_in_past(): void
    {
        $this->expectException(BookingValidationException::class);

        $this->bookingService->createBooking([
            'service_id' => $this->service->id,
            'start_time' => Carbon::yesterday()->setHour(10)->setMinute(0)
        ]);
    }

    public function test_cannot_double_book_slot(): void
    {
        $this->expectException(TimeSlotNotAvailableException::class);

        $startTime = Carbon::tomorrow()->setHour(10)->setMinute(0);

        // Create first booking
        $this->bookingService->createBooking([
            'service_id' => $this->service->id,
            'start_time' => $startTime->toDateTimeString()
        ]);

        // Try to create overlapping booking
        $this->bookingService->createBooking([
            'service_id' => $this->service->id,
            'start_time' => $startTime->copy()->addMinutes(30)->toDateTimeString()
        ]);
    }

    public function test_booking_creation_dispatches_event(): void
    {
        Event::fake();

        $startTime = Carbon::tomorrow()->setHour(10)->setMinute(0);

        $this->bookingService->createBooking([
            'service_id' => $this->service->id,
            'start_time' => $startTime->toDateTimeString()
        ]);

        Event::assertDispatched(BookingCreated::class);
    }
}