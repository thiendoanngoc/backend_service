<?php

use App\Models\Supply;
use Illuminate\Database\Seeder;

class AddDefaultSupplies extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$supply = new Supply();
		$supply->name = 'ink';
		$supply->price = 150000;
		$supply->creater_id = 1;
		$supply->save();

		$supply = new Supply();
		$supply->name = 'paper';
		$supply->price = 50000;
		$supply->creater_id = 1;
		$supply->save();
	}
}
