<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class UserLoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!auth()->attempt($request->only(['email', 'password']))) {
            throw ValidationException::withMessages([
                'email' => ['The credentials you entered are incorrect.']
            ]);
        }
        $user = Auth::guard('web')->user();

        $token = $user->createToken('user_token')->plainTextToken;

        return response()->json([
            'data' => $user,
            'message' => 'Login successful'
        ])->withCookie(cookie('token', $token, 60, null, null, false, true)); // httpOnlyをtrueに設定
    }
}

