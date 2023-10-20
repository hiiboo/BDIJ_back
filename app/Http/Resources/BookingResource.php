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
            'id' =>
            $this->id,
            'guide_id' =>
            $this->guide_id,
            'guest_id' =>
            $this->guest_id,
            'guest_first_name' =>
            optional($this->guest)->first_name, 
            'guest_last_name' =>
            optional($this->guest)->last_name,
            'guest_image' =>
            optional($this->guest)->profile_image,
            'guide_first_name' =>
            optional($this->guide)->first_name,
            'guide_last_name' =>
            optional($this->guide)->last_name,
            'guide_image' =>
            optional($this->guide)->profile_image,
            'status' =>
            $this->status,
            'guide_reviewed' =>
            $this->guide_reviewed,
            'guest_reviewed' =>
            $this->guest_reviewed,
            'start_time' =>
            $this->booking_start_time,
            'end_time' =>
            $this->booking_end_time,
            'actual_start_time' =>
            $this->actual_booking_start_time,
            // 'actual_end_time' =>
            // $this->actual_end_time,
            'comment' =>
            $this->comment,
            'total_guests' =>
            $this->total_guests,
            'total_amount' =>
            $this->total_amount,
            'guest_booking_confirmation' =>
            $this->guest_booking_confirmation,
            'guide_booking_confirmation' =>
            $this->guide_booking_confirmation,
            'start_confirmation' =>
            $this->start_confirmation,
            'deleted_at' =>
            $this->deleted_at,
            'created_at' =>
            $this->created_at,
            'updated_at' =>
            $this->updated_at,
        ];
    }
}
