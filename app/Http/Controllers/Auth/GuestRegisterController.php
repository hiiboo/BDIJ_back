<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Models\User;

class GuestRegisterController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(RegisterRequest $request)
    {
        $data = $request->getData();

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            // change the file name to $request->email
            $filename = $request->email . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profile_images', $filename, 'public');
            $data['profile_image'] = '/storage/' . $path;
        }

        $data['user_type'] = 'guest';

        $user = User::create($data);

        return response()->json([
            'data' => $user,
            'message' => 'Registration successful',
        ]);
    }
}
