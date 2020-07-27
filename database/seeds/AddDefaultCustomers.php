<?php

use App\Models\Customer;
use Illuminate\Database\Seeder;

class AddDefaultCustomers extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		for ($i = 0; $i < 10; $i++) {
			$customer = new Customer();
			$customer->full_name = 'Nguyen Van A' . $i;
			$customer->email = 'nguyenvana' . $i . '@abc.xyz';
			$customer->phone_number = '11' . $i;
			$customer->company_name = 'BitTo Solution';
			$customer->save();
		}
	}
}
