<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Log\Logger;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index']);
    }

    public function showRelatedUserAndBooking(Booking $booking)
    {
        $user = Auth::user();

        if ($user->isGuest()) {
            
            $booking->load(['guide', 'guest']);
        } elseif ($user->isGuide()) {
          
            $booking->load(['guest', 'guide']);
        } else {
            return response()->json(['error' => 'User is neither a guide nor a guest'], 403);
        }

        return response()->json(['data' => $booking], 200);
    }

    // show last booking for current user
    public function showLastBooking()
    {
        $user = Auth::user();

        if ($user->isGuest()) {
            $user->load('lastBookingAsGuest');
            $lastBooking = $user->lastBookingAsGuest;
        } elseif ($user->isGuide()) {
            $user->load('lastBookingAsGuide');
            $lastBooking = $user->lastBookingAsGuide;
        } else {
            return response()->json(['error' => 'User is neither a guide nor a guest'], 403);
        }

        // return $lastBooking;
        $lastBookingStatus = $lastBooking ? $lastBooking->status : null;
        return response()->json (['data'=> 
        $lastBooking], 200);
    }

    // show last booking status for current user
    public function showLastBookingStatus()
    {
        $user = Auth::user();

        if ($user->isGuest()) {
            $user->load('lastBookingAsGuest');
            $lastBooking = $user->lastBookingAsGuest;
        } elseif ($user->isGuide()) {
            $user->load('lastBookingAsGuide');
            $lastBooking = $user->lastBookingAsGuide;
        } else {
            return response()->json(['error' => 'User is neither a guide nor a guest'], 403);
        }

        // return $lastBooking; 
        $lastBookingStatus = $lastBooking ? $lastBooking->status : null;

        return response()->json(['data' => $lastBookingStatus], 200);
    }


    public function reserve(Request $request,$guide)
    {
        $guide = User::find($guide);

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
            Log::debug($request->user()->hasSpecificBookingsAsGuest());
            // use diffInHours method of Carbon,to calculate the difference between start_time and end_time
            $diffInMinutes = Carbon::parse($request->start_time)->diffInMinutes(Carbon::parse($request->end_time));
            $diffInHoursFloat = $diffInMinutes / 60.0;

            if ($request->total_guests >= 2) {
                $posted_hourly_rate = ($request->total_amount) / 0.75 / ($request->total_guests) / $diffInHoursFloat;
            } else {
                $posted_hourly_rate = $request->total_amount / $diffInHoursFloat;
            }

            $epsilon = 1;

            if (abs($posted_hourly_rate - $guide->hourly_rate) < $epsilon) {
            } else {
                return response()->json(['error' => 'Your hourly rate is not correct'], 403);
            }

            $booking = Booking::create([
                'guide_id' => $guide->id,
                'guest_id' => $request->user()->id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'status' => 'offer-pending',
                'total_guests' => $request->total_guests,
                'total_amount' => $request->total_amount,
                'comment' => $request->comment,
                'guest_booking_confirmation' => true,
            ]);

            // return $booking;
            return response()->json([
                'data' => $booking,
                'message' => 'success'
            ]);
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
                    'booking_status' => 'accepted',
                ]);
                return response()->json(['success' => 'Booking accepted successfully'], 200);
            }
        }
    }


    public function start(Request $request, Booking $booking)
    {

        if ($request->user()->isGuest()) {
            if ($booking->status === 'accepted') {
                $booking->update([
                    'start_confirmation' => true,
                    'guest_booking_confirmation' => false,
                    'guide_booking_confirmation' => false,
                    'status' => 'started',
                    'actual_start_time' => now(),
                ]);
                return response()->json(['success' => 'Booking started successfully'], 200);
            }
        }

    }

    public function finish(Request $request, Booking $booking)
    {
        //statusがstartedからfinishedに変更したいというリクエストが来た時、statusをfinishedにstart_confirmationをfalseに変更してbookings tableにあるbooking情報を更新する
        if ($request->user()->isGuest()) {
            if ($booking->status === 'started') {
                $booking->update([
                    'start_confirmation' => false,
                    'status' => 'finished',
                ]);
                return response()->json(['success' => 'Booking finished successfully'], 200);
            }
        }
    }

    public function getActualStartTime(Request $request, Booking $booking)
    {
        //return bookings tableからstart_time, end_time,actual_start_timeを、そしてnow()から現在時刻を一緒に返す
        $data=[
            "start_time" => $booking->start_time,
            "end_time" => $booking->end_time,
            "actual_start_time" => $booking->actual_start_time,
            "now" => now(),
        ];
        
        return response()->json(['data' => $data], 200);

    }
}
