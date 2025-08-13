<?php

namespace Tests\Unit;

use App\Models\Availability;
use App\Models\Service;
use Carbon\Carbon;
use Tests\TestCase;

class AvailabilityTest extends TestCase
{
    public function test_provider_can_set_recurring_availability()
    {
        $provider = $this->createProvider();
        $service = Service::factory()->create(['provider_id' => $provider->id]);

        $availability = Availability::create([
            'service_id' => $service->id,
            'day_of_week' => 'monday',
            'start_time' => '10:00',
            'end_time' => '14:00',
            'recurring' => true
        ]);

        $this->assertDatabaseHas('availabilities', [
            'id' => $availability->id,
            'day_of_week' => 'monday'
        ]);
    }
}