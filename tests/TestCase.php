<?php

namespace Tests;

use App\Models\User;
use App\Enums\RoleEnum;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function createAdmin(): User
    {
        return User::factory()->create([
            'role' => RoleEnum::ADMIN
        ]);
    }

    protected function createProvider(): User
    {
        return User::factory()->create([
            'role' => RoleEnum::PROVIDER
        ]);
    }

    protected function createCustomer(): User
    {
        return User::factory()->create([
            'role' => RoleEnum::CUSTOMER
        ]);
    }
}
