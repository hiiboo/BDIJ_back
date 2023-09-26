<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index']);
    }
    
    public function show(string $id)
    {
        //
    }

    public function reserve(Request $request,User $guide)
    {
        // if hasSpecificBookingsAsGuide is flase then return error sayng you can not book this guide,if hasSpecificBookingsAsGuide is true and hasSpecificBookingsAsGuest is ture then store the booking and also update the status of the booking to offer-pending and guest_booking_confirmation to true and return success message,if hasSpecificBookingsAsGuest is false then return error saying you can not book
        if ($request->user()->isGuest()) {

            if (!$guide->isGuide()) {
                return response()->json(['error' => 'Not a guide'], 403);
            }

            if (!$guide->hasSpecificBookingsAsGuide()) {
                return response()->json(['error' => 'You can not book this guide'], 403);
            }
            
            if(!$request->user()->hasSpecificBookingsAsGuest()){
                return response()->json(['error' => 'You can not book'], 403);
            }

            $booking = Booking::create([
                'guide_id' => 2,
                'guest_id' => $request->user()->id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'offer-pending',
                'guest_booking_confirmation' => true,
            ]);

            return response()->json(['success' => 'Booking created successfully'], 200);
        }
    }

    public function accept(Request $request, Booking $booking)
    {
        //statusがoffer-pendingからacceptに変更したいというリクエストが来た時、guide_booking_confirmationをtrueに変更し、statusをacceptedに変更してbookings tableにあるbooking情報を更新する
        Log::debug($booking);
        if ($request->user()->isGuide()) {
            if ($booking->status === 'offer-pending') {
                $booking->update([
                    'guide_booking_confirmation' => true,
                    'status' => 'accepted',
                ]);
                return response()->json(['success' => 'Booking accepted successfully'], 200);
            }
        }
    }


    public function update(Request $request, string $id)
    {
        //
    }

    public function startStatus(Request $request, string $id)
    {
        //
    }

    public function endStatus(Request $request, string $id)
    {
        //
    }

    public function getCurrentTime(Request $request, string $id)
    {
        //
    }
}
