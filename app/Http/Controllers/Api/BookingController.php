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
        //return bookings tableからactual_start_timeを、そしてnow()から現在時刻を一緒に返す
        return response()->json(['actual_start_time' => $booking->actual_start_time, 'now' => now()], 200);

    }
}
