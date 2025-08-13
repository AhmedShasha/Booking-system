<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'duration' => $this->duration,
            'category' => $this->category,
            'is_published' => $this->is_published,
            'provider' => new UserResource($this->whenLoaded('provider')),
            'availabilities' => AvailabilityResource::collection($this->whenLoaded('availabilities')),
            'bookings_count' => $this->whenLoaded('bookings', function () {
                return $this->bookings->count();
            }),
        ];
    }
}
