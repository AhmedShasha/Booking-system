<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class AvailableSlotResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'start_time' => Carbon::parse($this['start_time'])->format('Y-m-d H:i'),
            'end_time' => Carbon::parse($this['end_time'])->format('Y-m-d H:i'),
        ];
    }
}