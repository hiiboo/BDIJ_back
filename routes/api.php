<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\GuideController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\UserController;
use Ramsey\Uuid\Guid\Guid;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/check-auth', function () {
        return response()->json(['isLoggedIn' => true]);
    });
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Guide Routes guide resource only index, show, update
Route::apiResource('guide', GuideController::class)->only(['index', 'show', 'update']);

// route for getHourlyRate
Route::get('/guide/{guide}/hourly-rate', [GuideController::class, 'getHourlyRate'])
    ->name('guide.hourlyRate');

// showPrivate is a custom route
Route::get('/guide/{guide}/private', [GuideController::class, 'showPrivate'])
    ->name('guide.showPrivate');

Route::apiResource('guest', GuestController::class)->only(['show']);

// showPrivate
Route::get('/guest/{guest}/private', [GuestController::class, 'showPrivate'])
    ->name('guest.showPrivate');

// getCurrentUserLocation
Route::get('/user/current/location', [UserController::class, 'getCurrentUserLocation'])
    ->name('user.current.location');

// showCurrent
Route::get('/user/current', [UserController::class, 'showCurrent'])
    ->name('user.current');

//route for showBookingsAsGuide
Route::get('/user/current/bookings/guide', [GuideController::class, 'showBookingsAsGuide'])
    ->name('user.current.bookings.guide');

// route for showBookingsAsGuest
Route::get('/user/current/bookings/guest', [GuestController::class, 'showBookingsAsGuest'])
    ->name('user.current.bookings.guest');

Route::post('/bookings/{guide}/reserve', [BookingController::class, 'reserve'])
    ->name('bookings.reserve');

Route::patch('/bookings/{booking}/accept', [BookingController::class, 'accept'])
    ->name('bookings.accept');

Route::patch('/bookings/{booking}/start', [BookingController::class, 'start'])
    ->name('bookings.start');

Route::get('/bookings/{booking}/actual-start-time', [BookingController::class, 'getActualStartTime'])
    ->name('bookings.getActualStartTime');

Route::patch('/bookings/{booking}/finish', [BookingController::class, 'finish'])
    ->name('bookings.finish');

Route::post('/bookings/{booking}/reviews/guest', [ReviewController::class, 'postGuestReview'])
    ->name('reviews.guest');

Route::post('/bookings/{booking}/reviews/guide', [ReviewController::class, 'postGuideReview'])
    ->name('reviews.guide');

Route::get('/bookings/{booking}/related-user', [BookingController::class, 'showRelatedUserAndBooking'])
    ->name('booking.relatedUser');

    // route for showLastBooking
Route::get('/user/current/last-booking', [BookingController::class, 'showLastBooking'])
    ->name('user.current.lastBooking');

    // route for showLastBookingStatus
Route::get('/user/current/last-booking-status', [BookingController::class, 'showLastBookingStatus'])
    ->name('user.current.lastBookingStatus');

    // route for showMe
Route::get('/user/me', [UserController::class, 'showMe'])
    ->name('user.me');

    // route for update
Route::patch('/user/update', [UserController::class, 'update'])
    ->name('user.update');

// route for changeStatus
Route::patch('/user/change-status', [UserController::class, 'changeStatus'])
    ->name('user.changeStatus');