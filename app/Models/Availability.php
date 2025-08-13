<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'day_of_week',
        'start_time',
        'end_time',
        'recurring',
        'override_date'
    ];

    protected $casts = [
        'recurring' => 'boolean',
        'override_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
