<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class AddDefaultRoles extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$role = new Role();
		$role->role_name = 'Administrator';
		$role->save();

		$role = new Role();
		$role->role_name = 'Moderator';
		$role->save();
	}
}
