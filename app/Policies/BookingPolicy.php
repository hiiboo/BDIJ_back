<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    // policy for logged in guest
    public function showRelatedUserAndBooking(User $user, Booking $booking)
    {
        if ($user->isGuest()) {
            return $user->id === $booking->guest_id
                ? Response::allow()
                : Response::deny('You are not allowed to see this booking');
        } elseif ($user->isGuide()) {
            return $user->id === $booking->guide_id
                ? Response::allow()
                : Response::deny('You are not allowed to see this booking');
        } else {
            return Response::deny('User is neither a guide nor a guest');
        }
    }
}
