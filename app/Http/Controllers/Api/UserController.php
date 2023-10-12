<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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
            'guest_reviewed' => $user->guest_reviewed,
            'guide_reviewed' => $user->guide_reviewed,
            'profile_image' => $user->profile_image,
            'booking_status' => $lastBookingStatus,
        ];
        return response()->json([
            "data" => $data,
            "message" => "success"
        ]
        );
    }

    // return only if created_at is less than 15 min ago
    public function getCurrentUserLocation(Request $request)
    {
        $user = $request->user();
        $now = now();
        $createdAt = $user->created_at;
        $diff = $now->diffInMinutes($createdAt);
        Log::debug($diff);
        if ($diff < 15) {
            $data = [
                'latitude' => $user->latitude,
                'longitude' => $user->longitude,
            ];
            return response()->json([
                'data' => $data,
                'message' => 'success'
            ]);
        } else {
            return response()->json([
                'message' => 'error'
            ]);
        }
    }

    // use hasStatedBookingsAsGuide() in User.php
    public function update(Request $request)
    {
        //update current user's profile use UserResource
        $user = $request->user();
        Log::debug($user);
        Log::debug($user->hasStartedBookingsAsGuide());
        if ($user->hasStartedBookingsAsGuide()) {
            return response()->json([
                'message' => 'Updates are not allwed during a tour',
            ],
                403
            );
        }

        $data = $request->all();
        $user->update($data);
        return response()->json([
            'data' => $user,
            'message' => 'success'
        ]);
    }
    
    // get me (current user) 現在ログインしているユーザーを取得、
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
