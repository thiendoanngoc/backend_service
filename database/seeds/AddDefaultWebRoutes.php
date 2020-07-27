<?php

use App\Models\WebRoute;
use Illuminate\Database\Seeder;

class AddDefaultWebRoutes extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		WebRoute::insert([
			// 1
			['web_route_name' => 'admin.accounts.show-profile'],
			['web_route_name' => 'admin.accounts.update-profile'],

			// 3
			['web_route_name' => 'admin.roles.index'],
			['web_route_name' => 'admin.roles.show'],
			['web_route_name' => 'admin.roles.store'],
			['web_route_name' => 'admin.roles.update'],
			['web_route_name' => 'admin.roles.destroy'],

			// 8
			['web_route_name' => 'admin.settings.show'],
			['web_route_name' => 'admin.settings.update'],

			// 10
			['web_route_name' => 'admin.accounts.index'],
			['web_route_name' => 'admin.accounts.show'],
			['web_route_name' => 'admin.accounts.store'],
			['web_route_name' => 'admin.accounts.update'],
			['web_route_name' => 'admin.accounts.destroy'],

			// 15
			['web_route_name' => 'admin.staffs.index'],
			['web_route_name' => 'admin.staffs.show'],
			['web_route_name' => 'admin.staffs.store'],
			['web_route_name' => 'admin.staffs.update'],
			['web_route_name' => 'admin.staffs.destroy'],

			// 20
			['web_route_name' => 'admin.customers.index'],
			['web_route_name' => 'admin.customers.show'],
			['web_route_name' => 'admin.customers.store'],
			['web_route_name' => 'admin.customers.update'],
			['web_route_name' => 'admin.customers.destroy'],

			// 25
			['web_route_name' => 'admin.announcements.index'],
			['web_route_name' => 'admin.announcements.show'],
			['web_route_name' => 'admin.announcements.store'],
			['web_route_name' => 'admin.announcements.update'],
			['web_route_name' => 'admin.announcements.destroy'],

			// 30
			['web_route_name' => 'admin.departments.index'],
			['web_route_name' => 'admin.departments.show'],
			['web_route_name' => 'admin.departments.store'],
			['web_route_name' => 'admin.departments.update'],
			['web_route_name' => 'admin.departments.destroy'],

			// 35
			['web_route_name' => 'admin.contracts.index'],
			['web_route_name' => 'admin.contracts.show'],
			['web_route_name' => 'admin.contracts.store'],
			['web_route_name' => 'admin.contracts.update'],
			['web_route_name' => 'admin.contracts.destroy'],

			// 40
			['web_route_name' => 'admin.devices.index'],
			['web_route_name' => 'admin.devices.show'],
			['web_route_name' => 'admin.devices.store'],
			['web_route_name' => 'admin.devices.update'],
			['web_route_name' => 'admin.devices.destroy'],

			// 45
			['web_route_name' => 'admin.supplies.index'],
			['web_route_name' => 'admin.supplies.show'],
			['web_route_name' => 'admin.supplies.store'],
			['web_route_name' => 'admin.supplies.update'],
			['web_route_name' => 'admin.supplies.destroy'],

			// 50
			['web_route_name' => 'admin.supply-bills.index'],
			['web_route_name' => 'admin.supply-bills.show'],
			['web_route_name' => 'admin.supply-bills.store'],
			['web_route_name' => 'admin.supply-bills.update'],
			['web_route_name' => 'admin.supply-bills.destroy'],

			// 55
			['web_route_name' => 'admin.voting.index'],
			['web_route_name' => 'admin.voting.show'],
			['web_route_name' => 'admin.voting.store'],
			['web_route_name' => 'admin.voting.update'],
			['web_route_name' => 'admin.voting.destroy'],
		]);
	}
}
