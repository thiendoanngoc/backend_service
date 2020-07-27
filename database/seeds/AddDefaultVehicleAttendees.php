<?php

use App\Models\VehicleAttendee;
use Illuminate\Database\Seeder;

class AddDefaultVehicleAttendees extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehicleAttendee = new VehicleAttendee();
        $vehicleAttendee->vehicle_session_id = 1;
        $vehicleAttendee->attendee_id = 1;
        $vehicleAttendee->save();

        $vehicleAttendee = new VehicleAttendee();
        $vehicleAttendee->vehicle_session_id = 1;
        $vehicleAttendee->attendee_id = 2;
        $vehicleAttendee->save();

        $vehicleAttendee = new VehicleAttendee();
        $vehicleAttendee->vehicle_session_id = 2;
        $vehicleAttendee->attendee_id = 2;
        $vehicleAttendee->save();

        $vehicleAttendee = new VehicleAttendee();
        $vehicleAttendee->vehicle_session_id = 2;
        $vehicleAttendee->attendee_id = 3;
        $vehicleAttendee->save();
    }
}
