<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\GuideResource;
use App\Http\Resources\GuestResource;
use App\Http\Resources\BookingResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;


class GuideController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index','show']);
    }
    
    /**
     * Display a listing of the resource.
     */
    // get data for user_type = guide
    public function index()
    {
        $guides = User::where('user_type', 'guide')
        ->where('status', 'active')
            ->where(function ($query) {
        $query->whereDoesntHave('bookingsAsGuide')
            ->orWhereHas('bookingsAsGuide', function ($bookingQuery) {
                $bookingQuery->whereNull('status')
                    ->orWhere('status', 'reviewed')
                    ->orWhere('status', 'cancelled')
                    ->orWhere('guide_reviewed', true);
            });
        })->withTrashed()->get();

        return GuideResource::collection($guides);
    }

    /**
     * Display the specified resource.
     */

    public function showPrivate(User $guide)
    {
        if (!$guide->isGuide()) {
            return response()->json(['error' => 'Not a guide'], 403);
        }

        if (Auth::check()) {
            
            // Log::debug(Auth::user());
            $guide->load(['bookingsAsGuide' => function ($query) {
                $query->where('guide_id', Auth::id());
            }]);
        }

        return new GuideResource($guide);
    }

    public function show(User $guide)
    {
        if (!$guide->isGuide()) {
            return response()->json(['error' => 'Not a guide'], 403);
        }

        return new GuideResource($guide);
    }

    // 現在ログインしているユーザーがガイドの場合、そのユーザーに関連した全てのbookingsをGuestControllerで取得
    public function showBookingsAsGuide()
    {
        $user = Auth::user();

        if (!$user-> isGuide()) {
            return response()->json(['error' => 'Not a guest'], 403);
        }

        $bookings = $user->bookingsAsGuide;

        // Bookings resourceを使用して、bookingsをjson形式で返す
        return BookingResource::collection($bookings);
    }

    // guide_id を元に、Userから$guideを取得してその hourly_rateを取得
    public function getHourlyRate(User $guide)
    {
        return response()->json(['hourly_rate' => $guide->hourly_rate]);
    }


}
