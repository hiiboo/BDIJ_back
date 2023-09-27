<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\UserLoginController;
use App\Http\Controllers\Auth\UserLogoutController;
use App\Http\Controllers\Auth\GuideRegisterController;
use App\Http\Controllers\Auth\GuestRegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('auth')->group(function () {
    // User Routes
    Route::post('user/login', UserLoginController::class)->middleware('guest');
    Route::post('guide/register', GuideRegisterController::class)->middleware('guest');
    Route::post('guest/register', GuestRegisterController::class)->middleware('guest');
    Route::post('user/logout', UserLogoutController::class)->middleware('auth:sanctum');

});
