<?php

use App\Models\Device;
use Illuminate\Database\Seeder;

class AddDefaultDevices extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$device = new Device();
		$device->account_id = 1;
		$device->name = 'PC';
		$device->model = 'PC2520';
		$device->bought_date = '2019-10-12';
		$device->guarantee_date = '2020-10-12';
		$device->save();

		$device = new Device();
		$device->account_id = 2;
		$device->name = 'Laptop';
		$device->model = 'Dell 6666';
		$device->bought_date = '2020-07-12';
		$device->guarantee_date = '2025-07-12';
		$device->save();
	}
}
