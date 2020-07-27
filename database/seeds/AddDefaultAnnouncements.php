<?php

use App\Models\Announcement;
use Illuminate\Database\Seeder;

class AddDefaultAnnouncements extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$announcement = new Announcement();
		$announcement->content = 'This is announcement 1';
		$announcement->creater_id = 1;
		$announcement->save();

		$announcement = new Announcement();
		$announcement->content = 'This is announcement 2';
		$announcement->creater_id = 2;
		$announcement->save();
	}
}
