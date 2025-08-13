<?php

namespace Tests\Unit;

use App\Enums\RoleEnum;
use App\Models\User;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_has_correct_role()
    {
        $admin = $this->createAdmin();
        $provider = $this->createProvider();
        $customer = $this->createCustomer();

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($provider->isProvider());
        $this->assertTrue($customer->isCustomer());
    }

    public function test_user_timezone_is_set()
    {
        $user = User::factory()->create([
            'timezone' => 'Europe/London'
        ]);

        $this->assertEquals('Europe/London', $user->getTimezone());
    }
}