<?php

use App\Models\SupplyBill;
use Illuminate\Database\Seeder;

class AddDefaultSupplyBills extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$supplyBill = new SupplyBill();
		$supplyBill->supply_id = 1;
		$supplyBill->amount = 10;
		$supplyBill->total = 1500000;
		$supplyBill->save();

		$supplyBill = new SupplyBill();
		$supplyBill->supply_id = 2;
		$supplyBill->amount = 5;
		$supplyBill->total = 250000;
		$supplyBill->save();
	}
}
