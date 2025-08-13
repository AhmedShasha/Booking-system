<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailabilityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'day_of_week' => $this->day_of_week,
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'recurring' => $this->recurring,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'provider' => $this->whenLoaded('provider', fn() => [
                'id' => $this->provider->id,
                'name' => $this->provider->name,
            ]),
        ];
    }
}