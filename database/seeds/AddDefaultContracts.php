<?php

use App\Models\Contract;
use Illuminate\Database\Seeder;
use App\Enums\ContractStatusEnum;

class AddDefaultContracts extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$contract = new Contract();
		$contract->name = 'Hop dong 1';
		$contract->start_date = '2020-01-12';
		$contract->end_date = '2021-05-30';
		$contract->creater_id = 1;
		$contract->save();

		$contract = new Contract();
		$contract->name = 'Hop dong 2';
		$contract->start_date = '2020-07-23';
		$contract->end_date = '2023-05-30';
		$contract->creater_id = 2;
		$contract->save();

		$contract = new Contract();
		$contract->name = 'Hop dong 3';
		$contract->status = ContractStatusEnum::Completed;
		$contract->start_date = '2020-07-23';
		$contract->end_date = '2023-05-30';
		$contract->creater_id = 2;
		$contract->save();

		$contract = new Contract();
		$contract->name = 'Hop dong 4';
		$contract->status = ContractStatusEnum::Cancelled;
		$contract->start_date = '2020-07-23';
		$contract->end_date = '2023-05-30';
		$contract->creater_id = 2;
		$contract->save();
	}
}
