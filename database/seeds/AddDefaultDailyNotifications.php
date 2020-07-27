<?php

use App\Enums\DailyNotificationEnum;
use App\Http\Utils\Helpers;
use App\Models\DailyNotification;
use Illuminate\Database\Seeder;

class AddDefaultDailyNotifications extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		for ($i = 0; $i < 20; $i++) {
			$dailyNotification = new DailyNotification();
			$dailyNotification->type = DailyNotificationEnum::Slogan;
			$dailyNotification->content = 'Vestibulum tristique eget tellus nec iaculis. Proin volutpat lacus ut mi sagittis commodo. Morbi id felis euismod, gravida arcu a, faucibus ipsum. In quam lacus, lacinia sit amet lobortis eget, molestie nec quam. Fusce lacinia neque urna, et maximus nisl posuere vitae. Aliquam aliquam condimentum luctus. Etiam suscipit augue at dui pellentesque commodo. Nunc ut rhoncus mi. Nulla ut auctor tellus. (' . Helpers::getRandomString() . ')';
			$dailyNotification->save();

			$dailyNotification = new DailyNotification();
			$dailyNotification->type = DailyNotificationEnum::Habit;
			$dailyNotification->content = 'Vestibulum tristique eget tellus nec iaculis. Proin volutpat lacus ut mi sagittis commodo. Morbi id felis euismod, gravida arcu a, faucibus ipsum. In quam lacus, lacinia sit amet lobortis eget, molestie nec quam. Fusce lacinia neque urna, et maximus nisl posuere vitae. Aliquam aliquam condimentum luctus. Etiam suscipit augue at dui pellentesque commodo. Nunc ut rhoncus mi. Nulla ut auctor tellus. (' . Helpers::getRandomString() . ')';
			$dailyNotification->save();
		}
	}
}
