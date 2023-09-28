<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }
    //current userの関連するbookingを取得する
    public function showCurrent(Request $request)
    {
        $user = $request->user();
        
        if($user->isGuest()){
            $user->load('lastBookingAsGuest');
            $lastBooking = $user->lastBookingAsGuest;
        }else{
            $user->load('lastBookingAsGuide');
            $lastBooking = $user->lastBookingAsGuide;
        }

        $lastBookingStatus = $lastBooking ? $lastBooking->status : null;
        $data = [
            'id' => $user->id,
            'user_type' => $user->user_type,
            'user_status' => $user->status,
            'bookings_status' => $lastBookingStatus,
        ];
        return response()->json([
            "data" => $data,
            "message" => "success"
        ]
        );
    }

    public function getCurrentUserLocation(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'latitude' => $user->latitude,
            'longitude' => $user->longitude,
        ]);
    }

}
