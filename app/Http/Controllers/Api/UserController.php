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


    public function update(Request $request)
    {
        //update current user's profile use UserResource
        $user = $request->user();
        $data = $request->all();
        $user->update($data);
        return response()->json([
            'data' => $user,
            'message' => 'success'
        ]);
    }

    // get me (current user) Auth::user()を使用して、現在ログインしているユーザーを取得
    public function showMe(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'data' => $user,
            'message' => 'success'
        ]);
    }

    // change user status and return data and message
    public function changeStatus(Request $request)
    {
        $user = $request->user();
        $data = $request->all();
        $user->update($data);
        return response()->json([
            'data' => $user,
            'message' => 'success'
        ]);
    }

}
