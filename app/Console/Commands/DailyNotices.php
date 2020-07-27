<?php

namespace App\Console\Commands;

use App\Enums\DailyNotificationEnum;
use App\Http\Utils\Helpers;
use App\Models\Account;
use App\Models\AccountSession;
use App\Models\DailyNotification;
use Illuminate\Console\Command;

class DailyNotices extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'daily-notices';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Daily Notices';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$arrConnections = array();
		Helpers::getAllDatabases($arrConnections);

		foreach ($arrConnections as $connection) {
			$dbDailyNotification = new DailyNotification();
			$dbDailyNotification->setConnection($connection);

			$dailySlogans = $dbDailyNotification
				->where('type', DailyNotificationEnum::Slogan)
				->pluck('content')->toArray();

			$dailyHabits = $dbDailyNotification
				->where('type', DailyNotificationEnum::Habit)
				->pluck('content')->toArray();

			$dbAccountSession = new AccountSession();
			$dbAccountSession->setConnection($connection);

			$dbAccount = new Account();
			$dbAccount->setConnection($connection);

			$birthdayFCMTokens = array();
			$accounts = $dbAccount
				->join('account_sessions', 'accounts.id', '=', 'account_sessions.account_id')
				->get(['birthday', 'fcm_token']);

			$currentDate = date('m-d');
			foreach ($accounts as $account) {
				if ($currentDate === substr($account->birthday, 5, 5)) {
					array_push($birthdayFCMTokens, $account->fcm_token);
				} else {
					if ($currentDate === '02-28' && substr($account->birthday, 5, 5) === '02-29') {
						array_push($birthdayFCMTokens, $account->fcm_token);
					}
				}
			}

			$fcmTokens = $dbAccountSession->pluck('fcm_token')->toArray();

			Helpers::pushNotifications($fcmTokens, $dailySlogans[rand(0, count($dailySlogans) - 1)], 'Slogan mỗi ngày');
			Helpers::pushNotifications($fcmTokens, $dailyHabits[rand(0, count($dailyHabits) - 1)], 'Thói quen mỗi ngày');
			Helpers::pushNotifications($birthdayFCMTokens, 'Mừng ngày bé sinh ra đời, nào mình cùng nắm tay tười cười, ...', 'Happy Birthday!');
		}
	}
}
