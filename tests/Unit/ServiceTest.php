<?php

namespace Tests\Unit;

use App\Models\Service;
use Tests\TestCase;

class ServiceTest extends TestCase
{
    public function test_provider_can_create_service()
    {
        $provider = $this->createProvider();
        
        $service = Service::factory()->create([
            'provider_id' => $provider->id,
            'is_published' => true
        ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'provider_id' => $provider->id
        ]);
    }

    public function test_only_published_services_are_visible()
    {
        Service::factory()->count(3)->create(['is_published' => true]);
        Service::factory()->count(2)->create(['is_published' => false]);

        $publishedCount = Service::published()->count();
        
        $this->assertEquals(3, $publishedCount);
    }
}