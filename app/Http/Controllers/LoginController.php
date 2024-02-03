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
        ])

        if(!$user) {
            return response()->json(['message' => 'Could not process a user with that phone number'], 401);
        }

        // send the user a one time code
        $user->notify(new LoginNeedsVerification());    // send an instantiated LoginNeedsVerification object

        // return a response
        return response()->json(['message' => 'Text message notification sent']);
    }
}
