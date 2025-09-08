<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
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
            'status' => $this->status,
            'start_time' => $this->start_time->format('Y-m-d H:i'),
            'end_time' => $this->end_time->format('Y-m-d H:i'),
            'user' => new UserResource($this->whenLoaded('user')),
            'service' => new ServiceResource($this->whenLoaded('service')),
            'provider' => new UserResource($this->whenLoaded('provider')),
            'availability' => new AvailabilityResource($this->whenLoaded('availability')),
        ];
    }
}
