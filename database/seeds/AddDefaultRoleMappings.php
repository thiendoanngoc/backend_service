<?php

use App\Enums\RoleEnum;
use App\Models\RoleMapping;
use Illuminate\Database\Seeder;

class AddDefaultRoleMappings extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$roleMapping = new RoleMapping();
		$roleMapping->account_id = 1;
		$roleMapping->role_id = RoleEnum::Administrator;
		$roleMapping->save();
	}
}
