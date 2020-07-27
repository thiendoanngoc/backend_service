<?php

use App\Models\Setting;
use Illuminate\Database\Seeder;

class AddDefaultSettings extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$setting = new Setting();
		$setting->app_name = config('app.name');
		$setting->app_slogan = 'Tasks Office - Quản lý tác vụ văn phòng';
		$setting->app_owner = 'BitTo Solution';
		$setting->email = 'hung@bittosolution.vn';
		$setting->phone_number = '0359384509';
		$setting->address = 'Tokyo, Akihabara';
		$setting->save();
	}
}
