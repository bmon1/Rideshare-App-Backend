<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function show(Request $request) {
        // return the user and associated driver model
        $user = $request->user();
        $user->load('driver');          // takes the relationship ('driver') and determines if there 
                                        // is a driver relationship on the model its called on ($user) then attempts to 
                                        // load it (if it exists) and inject it as a property on the user object

        return $user;                   // $user now has property $user->driver that will be null or the 
                                        // associated driver object of this user
    }

    public function update(Request $request) {
        // validate request
        $request->validate([
            'year' => 'required|gt:2010',
            'make' => 'required',
            'model' => 'required',
            'color' => 'required',
            'license_plate' => 'required',
            'name' => 'required',
        ]);

        // update user object (only attribute associated with user is 'name')
        $user = $request->user();

        $user->update($request->only('name'));

        // create or update a driver associated with this user
        $user->driver()->update($request->only([
            'year',
            'make',
            'model',
            'color',
            'license_plate',
        ]));

        $user->load('driver');          // grabs driver object and attaches it to the user model

        return $user;

    }
}
