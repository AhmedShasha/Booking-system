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
            'user_id' => $this->user_id,
            'service_id' => $this->service_id,
            'provider_id' => $this->provider_id,
            'status' => $this->status,
            'date' => $this->date,
            'time' => $this->time,
            'user' => new UserResource($this->whenLoaded('user')),
            'service' => new ServiceResource($this->whenLoaded('service')),
            'provider' => new UserResource($this->whenLoaded('provider')),
            'availability' => new AvailabilityResource($this->whenLoaded('availability')),
        ];
    }
}
