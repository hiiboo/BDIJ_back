<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\GuideController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\ReviewController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Guide Routes guide resource only index, show, update
Route::apiResource('guide', GuideController::class)->only(['index', 'show', 'update']);

Route::apiResource('guest', GuestController::class)->only(['show']);

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