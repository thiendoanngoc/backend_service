<?php

namespace App\Console\Commands;

use App\Http\Utils\Helpers;
use App\Models\Contract;
use Illuminate\Console\Command;

class ContractReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contract-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Contract Reminder';

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

		$currentTime = date('Y-m-d');
		$after3Day = date('Y-m-d', strtotime('+3 days', strtotime($currentTime)));

		foreach ($arrConnections as $connection) {
			$dbContract = new Contract();
			$dbContract->setConnection($connection);

			$contracts = $dbContract->where('end_date', '<=', $after3Day)->get();

			foreach ($contracts as $contract) {
				$contract->notifyToCreater($connection, trans('I006'));
			}
		}
    }
}
