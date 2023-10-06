<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Review;


class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index']);
    }
    
    public function postGuestReview(Request $request, Booking $booking)
    {
       
        $guest = $request->user();

        if (!$guest->isGuest()) {
            return response()->json(['error' => 'Only guests can post reviews.'], 403);
        }

        $data = $request->validate([
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $review = new Review();
        $review->reviewer_id = $guest->id;
        $review->reviewee_id = $booking->guide_id;
        $review->booking_id = $booking->id;
        $review->content = $data['content'];
        $review->rating = $data['rating'];
        $review->save();

        $booking->guest_reviewed = true;
        $booking->save();

        if($booking->guide_reviewed == true){
            $booking->status = 'reviewed';
            $booking->guest_reviewed = false;
            $booking->guide_reviewed = false;
            $booking->save();
        }

        return response()->json(['success' => 'Guest successfully posted review',
            'guest_reviewed' => true
        ], 200);

    }

    public function postGuideReview(Request $request, Booking $booking)
    {
        $guide = $request->user();

        if (!$guide->isGuide()) {
            return response()->json(['error' => 'Only guides can post reviews.'], 403);
        }

        // Validate the request data
        $data = $request->validate([
            'content' => 'required|string',
            'rating' => 'required|integer|min:1|max:5'
        ]);

        $review = new Review();
        $review->reviewer_id = $guide->id;  
        $review->reviewee_id = $booking->guest_id;
        $review->booking_id = $booking->id;
        $review->content = $data['content'];
        $review->rating = $data['rating'];
        $review->save();

        $booking->guide_reviewed = true;
        $booking->save();

        if($booking->guest_reviewed == true){
            $booking->status = 'reviewed';
            $booking->guest_reviewed = false;
            $booking->guide_reviewed = false;
            $booking->save();
        }
        // guide post review
        return response()->json(['success' => 'Guide successfully posted review',
    'guide_reviewed' => true], 200);
    }
}
