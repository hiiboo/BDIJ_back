<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\BookingResource;

class GuideResource extends JsonResource
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
            'first_name' => (string) $this->first_name,
            'last_name' => (string) $this->last_name,
            'email' => (string) $this->email,
            // 'reviews' => ReviewResource::collection($this->whenLoaded('reviewer')),
            'bookings' => BookingResource::collection($this->whenLoaded('bookingsAsGuide')),
        ];
    }
}
