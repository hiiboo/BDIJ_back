<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\GuestResource;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GuestController extends Controller
{
     public function __construct(){
        $this->middleware('auth:sanctum')->except(['index']);
     }

    /**
     * Display the specified resource.
     */
    public function show(User $guest)
    {
        if (!$guest->isGuest()) {
            return response()->json(['error' => 'Not a guest'], 403);
        }

        if (Auth::check()) {
            Log::debug(Auth::user());
            $guest->load(['bookingsAsGuest' => function ($query) {
                $query->where('guest_id', Auth::id());
            }]);
        }

        return new GuestResource($guest);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

}
