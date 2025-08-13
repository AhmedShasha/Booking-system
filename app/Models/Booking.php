<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'service_id',
        'start_time',
        'end_time',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => BookingStatus::class,
    ];

    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d\TH:i:sP');
    }

    public function getStartTimeAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->setTimezone($this->service->provider->getTimezone());
        }
        return null;
    }

    public function getEndTimeAttribute($value)
    {
        if ($value) {
            return Carbon::parse($value)->setTimezone($this->service->provider->getTimezone());
        }
        return null;
    }

    public function isPending(): bool
    {
        return $this->status === BookingStatus::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this->status === BookingStatus::CONFIRMED;
    }

    public function isCancelled(): bool
    {
        return $this->status === BookingStatus::CANCELLED;
    }

    public function isCompleted(): bool
    {
        return $this->status === BookingStatus::COMPLETED;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
