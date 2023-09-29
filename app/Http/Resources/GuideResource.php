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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'gender' => $this->gender,
            'profile_image' => $this->profile_image,
            'level' => $this->level,
            'introduction' => $this->introduction,
            'hourly_rate' => $this->hourly_rate,
            'birthday' => $this->birthday,
            'occupation' => $this->occupation,
            'user_type' => $this->user_type,
            'status' => $this->status,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'email' => $this->email,
            // 'reviews' => ReviewResource::collection($this->whenLoaded('reviewer')),
            'bookings' => BookingResource::collection($this->whenLoaded('bookingsAsGuide')),
            // load average rating and review count
            'review_average' => $this->review_average,
            'review_count' => $this->review_count,
        ];
    }
}
