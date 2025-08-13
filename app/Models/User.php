<?php

namespace App\Models;

use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'timezone'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'role' => RoleEnum::class
    ];

    public function isAdmin(): bool
    {
        return $this->role === RoleEnum::ADMIN;
    }

    public function isProvider(): bool
    {
        return $this->role === RoleEnum::PROVIDER;
    }

    public function isCustomer(): bool
    {
        return $this->role === RoleEnum::CUSTOMER;
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'provider_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function availabilities()
    {
        return $this->hasManyThrough(Availability::class, Service::class, 'provider_id', 'service_id');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function getTimezone(): string
    {
        return $this->timezone ?? config('app.timezone');
    }
}
