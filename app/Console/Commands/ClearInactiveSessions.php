<?php

namespace App\Console\Commands;

use App\Http\Utils\Helpers;
use App\Models\AccountSession;
use Illuminate\Console\Command;

class ClearInactiveSessions extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'clear-inactive-sessions';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Clear Inactive Sessions';

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

		$dayBefore = date('Y-m-d H:i:s', strtotime('-15 minutes'));

		foreach ($arrConnections as $connection) {
			$dbAccountSession = new AccountSession();
			$dbAccountSession->setConnection($connection);

			$accountSessions = $dbAccountSession->where('is_remember', 0)
				->where('last_active', '<', $dayBefore)->get();

			foreach ($accountSessions as $accountSession) {
				$accountSession->delete();
			}
		}
	}
}
