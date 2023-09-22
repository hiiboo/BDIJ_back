<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserLogoutController extends Controller
{

    public function __invoke(Request $request)
    {
        $user = $request->user('web');
        $user->tokens()->delete();

        auth()->guard('web')->logout();

        // httpOnlyのトークンcookieを削除する
        $cookie = cookie('token', '', -1);

        return response()->json([
            'message' => 'Logout successful'
        ])->withCookie($cookie);
    }
}
