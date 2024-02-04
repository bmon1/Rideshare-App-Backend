<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request) {
        // validate request
        $request->validate([
            'origin' => 'required',
            'destination' => 'required',
            'destination_name' => 'required',
        ]);

        // create trip
        return $request->user()->trips()->only([            // creates a trip as a passenger for the user making
            'origin' => 'required',                         // the request and returns the trip to the client.
            'destination' => 'required',                    // trips() comes from the relationship in the driver model
            'destination_name' => 'required',
        ]);
    }

    public function show(Request $request, Trip $trip) {
        // is the trip associated with the authenticated user? (if the request is from the passenger)
        if($trip->user->id == $request->user()->id) {
            return $trip;
        }

        if($trip->driver && $request->user()->driver) {     // need this 'if' because driver and user driver could be 
                                                            // null if trip has not been accepted yet

            // is the trip associated with the authenticated user? (if the request is from the driver)
            if($trip->driver->id == $request->user()->driver->id) {
                return $trip;
            }
        }

        return response()->json(['message' => 'Cannot find this trip.'], 404);
    }

    public function accept(Request $request, Trip $trip) {
        // a driver accepts a trip (driver making the request)

        // validate request (only need to validate drivers location)
        $request->validate([
            'driver_location' => 'required',
        ]);

        // update trip model with a driver's id
        $trip->update([
            'driver_id' => $request->user()->id,
            'driver_location' => $request->driver_location,
        ]);

        $trip->load('driver.user');             // add driver object to the trip object so passenger can get driver info
                                                // also adds the user object to driver object
                                                
        
        return $trip;
    }

    public function start(Request $request, Trip $trip) {
        // a driver has started taking a passenger to their destination

    }

    public function end(Request $request, Trip $trip) {
        // a driver has ended a trip

    }

    public function location(Request $request, Trip $trip) {
        // update a drivers current location

    }
}
