<?php

use Carbon\Carbon;
use App\Models\VehicleSession;
use Illuminate\Database\Seeder;

class AddDefaultVehicleSessions extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $start = Carbon::parse('2020-04-15 09:00:00');
        $end = Carbon::parse('2020-04-15 10:00:00');
        $vehicleSession = new VehicleSession();
        $vehicleSession->vehicle_id = 1;
        $vehicleSession->booker_id = 1;
        $vehicleSession->booking_start = $start;
        $vehicleSession->booking_end = $end;
        $vehicleSession->save();

        $start = Carbon::parse('2020-04-15 10:00:00');
        $end = Carbon::parse('2020-04-15 11:00:00');
        $vehicleSession = new VehicleSession();
        $vehicleSession->vehicle_id = 2;
        $vehicleSession->booker_id = 1;
        $vehicleSession->booking_start = $start;
        $vehicleSession->booking_end = $end;
        $vehicleSession->save();
    }
}
