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
        $guides = User::hasSpecificBookingsAsGuide()->get();

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


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

}
