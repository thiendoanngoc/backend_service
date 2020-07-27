<?php

namespace App\Console\Commands;

use App\Enums\TaskStatusEnum;
use App\Http\Utils\Helpers;
use App\Models\Task;
use Illuminate\Console\Command;

class TaskReminder extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'task-reminder';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Task Reminder';

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

		$currentTime = date('Y-m-d H:i:s');
		$after15Minutes = date('Y-m-d H:i:s', strtotime('+15 minutes', strtotime($currentTime)));

		foreach ($arrConnections as $connection) {
			$dbTask = new Task();
			$dbTask->setConnection($connection);

			$tasks = $dbTask->where('status', '!=', TaskStatusEnum::Cancel)
				->where('status', '!=', TaskStatusEnum::Done)
				->where('reminder_before_time', '>=', $currentTime)
				->where('reminder_before_time', '<', $after15Minutes)->get();

			foreach ($tasks as $task) {
				$task->notifyAllStakeHolders($connection, trans('I004'));
			}
		}
	}
}
