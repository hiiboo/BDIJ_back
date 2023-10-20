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
use App\Notifications\BookingReceived;
use App\Notifications\BookingAccepted;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index']);
    }

    public function showRelatedUserAndBooking(Booking $booking)
    {
        // use policy showRelatedUserAndBooking
        $this->authorize('showRelatedUserAndBooking', $booking);
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
            // add guest image to $lastBooking 
            $lastBooking->load('guest');
        } elseif ($user->isGuide()) {
            $user->load('lastBookingAsGuide');
            $lastBooking = $user->lastBookingAsGuide;
            // add guide image to $lastBooking
            $lastBooking->load('guide');
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

            // use BookingReceived notification
            // $guide->notify(new BookingReceived($booking));

            // return $booking;
            return response()->json([
                'data' => $booking,
                'message' => 'success'
            ]);
        }
    }

    // create guide cancel method and huest cancel method depending on guest or guide use canCancelBookingAsGuide method of User model and if it returns true, update status to cancelled and change guest_booking_confirmation to false and change total_amount to 0 and send response with success cancel message
    public function cancel(Request $request, Booking $booking)
    {
        if ($request->user()->isGuide()) {
            if ($request->user()->canCancelBookingAsGuide($booking)) {
                $booking->update([
                    'status' => 'cancelled',
                    'guest_booking_confirmation' => false,
                    'total_amount' => 0,
                ]);
                return response()->json(['success' => 'Booking cancelled successfully'], 200);
            }
        }
        if ($request->user()->isGuest()) {
            if ($request->user()->canCancelBookingAsGuest($booking)) {
                $booking->update([
                    'status' => 'cancelled',
                    'guide_booking_confirmation' => false,
                    'guest_booking_confirmation' => false,
                    'total_amount' => $booking->calculateCancellationTotalAmount(),
                ]);
                return response()->json(['success' => 'Booking cancelled successfully'], 200);
            }
        } else {
            return response()->json(['error' => 'User is neither a guide nor a guest or not logged in'], 403);
        }
    }
    
    public function accept(Request $request, Booking $booking)
    {
        //statusがoffer-pendingからacceptに変更したいというリクエストが来た時、guide_booking_confirmationをtrueに変更し、statusをacceptedに変更してbookings tableにあるbooking情報を更新する
        // Log::debug($booking);
        if ($request->user()->isGuide()) {
            if ($booking->status === 'offer-pending') {
                $booking->update([
                    'guide_booking_confirmation' => true,
                    'status' => 'accepted',
                ]);
                // send email to guest saying that booking has been cancelled using BookingAccepted notification wih mailtrap
                // $booking->guest->notify(new BookingAccepted($booking));
                // return message and booking data
                return response()->json([
                    'data' => $booking,
                    'message' => 'success'
                ]);
            } else {
                return response()->json([
                    'message' => 'booking status is not offer-pending'
                ]);
            }   
        } else {
            return response()->json([
                'message' => 'you are not a guide or not logged in as a guide' 
            ]);
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
                 // return message and booking data
                return response()->json([
                    'data' => $booking,
                    'message' => 'success'
                ]);
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
                  // return message and booking data
                return response()->json([
                    'data' => $booking,
                    'message' => 'booking finished successfully'
                ]);
            }
        }
    }

    public function getActualStartTime(Request $request, Booking $booking)
    {
        //return bookings tableからstart_time, end_time,actual_start_timeを、そしてnow()から現在時刻を一緒に返す
        $data=[
            "start_time" => $booking->booking_start_time,
            "end_time" => $booking->booking_end_time,
            "actual_start_time" => $booking->actual_booking_start_time,
            "now" => now(),
        ];
          // return message and booking data
        return response()->json([
            'data' => $data,
            'message' => 'success'
        ]);

    }
}
