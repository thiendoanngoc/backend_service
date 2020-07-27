<?php

use App\Enums\TaskRoleEnum;
use App\Models\TaskAssignee;
use Illuminate\Database\Seeder;

class AddDefaultTaskAssignees extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$taskAssignee = new TaskAssignee();
		$taskAssignee->task_id = 1;
		$taskAssignee->account_id = 2;
		$taskAssignee->task_role = TaskRoleEnum::Hoster;
		$taskAssignee->save();

		$taskAssignee = new TaskAssignee();
		$taskAssignee->task_id = 1;
		$taskAssignee->account_id = 3;
		$taskAssignee->task_role = TaskRoleEnum::Supporter;
		$taskAssignee->save();

		$taskAssignee = new TaskAssignee();
		$taskAssignee->task_id = 2;
		$taskAssignee->account_id = 2;
		$taskAssignee->task_role = TaskRoleEnum::Hoster;
		$taskAssignee->save();

		$taskAssignee = new TaskAssignee();
		$taskAssignee->task_id = 2;
		$taskAssignee->account_id = 3;
		$taskAssignee->task_role = TaskRoleEnum::Supporter;
		$taskAssignee->save();

		$taskAssignee = new TaskAssignee();
		$taskAssignee->task_id = 3;
		$taskAssignee->account_id = 2;
		$taskAssignee->task_role = TaskRoleEnum::Hoster;
		$taskAssignee->save();

		$taskAssignee = new TaskAssignee();
		$taskAssignee->task_id = 4;
		$taskAssignee->account_id = 2;
		$taskAssignee->task_role = TaskRoleEnum::Hoster;
		$taskAssignee->save();

		$taskAssignee = new TaskAssignee();
		$taskAssignee->task_id = 5;
		$taskAssignee->account_id = 2;
		$taskAssignee->task_role = TaskRoleEnum::Hoster;
		$taskAssignee->save();

		$taskAssignee = new TaskAssignee();
		$taskAssignee->task_id = 6;
		$taskAssignee->account_id = 2;
		$taskAssignee->task_role = TaskRoleEnum::Hoster;
		$taskAssignee->save();

		$taskAssignee = new TaskAssignee();
		$taskAssignee->task_id = 7;
		$taskAssignee->account_id = 2;
		$taskAssignee->task_role = TaskRoleEnum::Hoster;
		$taskAssignee->save();
	}
}
