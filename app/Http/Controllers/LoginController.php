<?php

namespace App\Http\Controllers;

use App\Notifications\LoginNeedsVerification;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function submit(Request $request) {
        // validate phone number
        $request->validate([
            'phone' => 'required|numberic|min:10'
        ]);

        // find / create a user model
        $user = User::findOrCreate([
            'phone' => $request->phone
        ]);

        if(!$user) {
            return response()->json(['message' => 'Could not process a user with that phone number'], 401);
        };

        // send the user a one time code
        $user->notify(new LoginNeedsVerification());    // send an instantiated LoginNeedsVerification object

        // return a response
        return response()->json(['message' => 'Text message notification sent']);
    }

    public function verify(Request $request) {
        // validate incoming request
        $request->validate([
            'phone' => 'required|numeric|min:100',
            'login_code' => 'required|numeric|between:111111,999999',
        ]);

        // find the user
        $user = User::where('phone', $request->phone)
            ->where('login_code', $request->login_code)
            ->first();
        // is code provided the same one saved?
        // if so, update $user->login_code to null to avoid reuse, then return an auth token
        if($user) {
            $user->update([
                'login_code' => null
            ]);

            return $user->createToken($request->login_code)->plainTextToken;
        };

        // if not, return back a message
        return response()->json(['message' => 'Invalid login code'], 401);
    }
}
