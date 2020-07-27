<?php

use App\Models\Permission;
use Illuminate\Database\Seeder;

class AddDefaultPermissions extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$currentDB = config('database.default');

		if ($currentDB !== config('app.master_db')) {
			Permission::insert([
				['role_id' => 1, 'web_route_id' => 1],
				['role_id' => 1, 'web_route_id' => 2],

				['role_id' => 1, 'web_route_id' => 3],
				['role_id' => 1, 'web_route_id' => 4],
				['role_id' => 1, 'web_route_id' => 5],
				['role_id' => 1, 'web_route_id' => 6],

				['role_id' => 1, 'web_route_id' => 8],

				['role_id' => 1, 'web_route_id' => 10],
				['role_id' => 1, 'web_route_id' => 11],
				['role_id' => 1, 'web_route_id' => 12],
				['role_id' => 1, 'web_route_id' => 13],

				['role_id' => 1, 'web_route_id' => 15],
				['role_id' => 1, 'web_route_id' => 16],
				['role_id' => 1, 'web_route_id' => 17],
				['role_id' => 1, 'web_route_id' => 18],

				['role_id' => 1, 'web_route_id' => 20],
				['role_id' => 1, 'web_route_id' => 21],
				['role_id' => 1, 'web_route_id' => 22],
				['role_id' => 1, 'web_route_id' => 23],

				['role_id' => 1, 'web_route_id' => 25],
				['role_id' => 1, 'web_route_id' => 26],
				['role_id' => 1, 'web_route_id' => 27],
				['role_id' => 1, 'web_route_id' => 28],

				['role_id' => 1, 'web_route_id' => 30],
				['role_id' => 1, 'web_route_id' => 31],
				['role_id' => 1, 'web_route_id' => 32],
				['role_id' => 1, 'web_route_id' => 33],

				['role_id' => 1, 'web_route_id' => 35],
				['role_id' => 1, 'web_route_id' => 36],
				['role_id' => 1, 'web_route_id' => 37],
				['role_id' => 1, 'web_route_id' => 38],

				['role_id' => 1, 'web_route_id' => 40],
				['role_id' => 1, 'web_route_id' => 41],
				['role_id' => 1, 'web_route_id' => 42],
				['role_id' => 1, 'web_route_id' => 43],

				['role_id' => 1, 'web_route_id' => 45],
				['role_id' => 1, 'web_route_id' => 46],
				['role_id' => 1, 'web_route_id' => 47],
				['role_id' => 1, 'web_route_id' => 48],

				['role_id' => 1, 'web_route_id' => 50],
				['role_id' => 1, 'web_route_id' => 51],
				['role_id' => 1, 'web_route_id' => 52],
				['role_id' => 1, 'web_route_id' => 54],

				['role_id' => 1, 'web_route_id' => 55],
				['role_id' => 1, 'web_route_id' => 56],
				['role_id' => 1, 'web_route_id' => 57],
				['role_id' => 1, 'web_route_id' => 58],
				['role_id' => 1, 'web_route_id' => 59],
			]);
		}
	}
}
