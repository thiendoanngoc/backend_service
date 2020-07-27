<?php

namespace App\Console;

use App\Console\Commands\ClearInactiveSessions;
use App\Console\Commands\DailyNotices;
use App\Console\Commands\TaskReminder;
use App\Console\Commands\ContractReminder;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		ClearInactiveSessions::class,
		DailyNotices::class,
		TaskReminder::class,
		ContractReminder::class
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		$schedule->command('clear-inactive-sessions')->everyFifteenMinutes()->runInBackground();
		$schedule->command('task-reminder')->everyFifteenMinutes()->runInBackground();
		$schedule->command('daily-notices')->daily()->runInBackground();
		$schedule->command('contract-reminder')->daily()->runInBackground();
	}

	/**
	 * Register the commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{
		$this->load(__DIR__ . '/Commands');

		require base_path('routes/console.php');
	}
}
