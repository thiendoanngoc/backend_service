<?php

use App\Models\Vehicle;
use Illuminate\Database\Seeder;

class AddDefaultVehicles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $vehicle = new Vehicle();
        $vehicle->vehicle_name = 'car 001';
        $vehicle->vehicle_number = '51ABCXYZ';
        $vehicle->save();

        $vehicle = new Vehicle();
        $vehicle->vehicle_name = 'car 002';
        $vehicle->vehicle_number = '51DEFWUT';
        $vehicle->save();
    }
}
