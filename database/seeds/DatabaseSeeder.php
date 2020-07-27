<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Seed the application's database.
	 *
	 * @return void
	 */
	public function run()
	{
		$currentDB = config('database.default');

		if ($currentDB === config('app.master_db')) {
			$this->call([
				AddDefaultSettings::class,
				AddDefaultAccounts::class,
				AddDefaultRoles::class,
				AddDefaultWebRoutes::class,
				AddDefaultPermissions::class,
				AddDefaultRoleMappings::class,
			]);
		} else {
			$this->call([
				AddDefaultDepartments::class,
				AddDefaultPositions::class,
				AddDefaultAccounts::class,
				AddDefaultStaffs::class,
				AddDefaultCustomers::class,
				AddDefaultRoles::class,
				AddDefaultWebRoutes::class,
				AddDefaultPermissions::class,
				AddDefaultRoleMappings::class,
				AddDefaultTaskGroups::class,
				AddDefaultTasks::class,
				AddDefaultTaskAssignees::class,
				AddDefaultUnconfirmTasks::class,
				AddDefaultDailyNotifications::class,
				AddDefaultAnnouncements::class,
				AddDefaultSupplies::class,
				AddDefaultSupplyBills::class,
				AddDefaultVotingOptions::class,
				AddDefaultVotingTopics::class,
				AddDefaultVotingBindings::class,
				AddDefaultVoters::class,
				AddDefaultDevices::class,
				AddDefaultContracts::class,
				AddDefaultRooms::class,
				AddDefaultMeetingSessions::class,
				AddDefaultRoomAttendees::class,
				AddDefaultVehicles::class,
				AddDefaultVehicleSessions::class,
				AddDefaultVehicleAttendees::class,
				AddDefaultStuffs::class,
				AddDefaultNotifications::class,
				AddDefaultCanteenRegistrations::class
			]);
		}
	}
}
