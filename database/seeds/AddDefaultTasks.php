<?php

use Carbon\Carbon;
use App\Enums\TaskStatusEnum;
use App\Models\Task;
use Illuminate\Database\Seeder;

class AddDefaultTasks extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$dt = Carbon::parse('2020-10-28 16:00:00');
		$task = new Task();
		$task->title = 'task 1';
		$task->description = 'task 1 waiting';
		$task->task_group_id = 1;
		$task->end_time = $dt;
		$task->creater_id = 1;
		$task->created_at = Carbon::parse('2020-04-01 09:00:00');
		$task->updated_at = Carbon::parse('2020-04-01 09:00:00');
		$task->save();

		$dt = Carbon::parse('2020-10-20 16:00:00');
		$task = new Task();
		$task->title = 'task 2';
		$task->description = 'task 2 waiting';
		$task->task_group_id = 1;
		$task->end_time = $dt;
		$task->creater_id = 1;
		$task->created_at = Carbon::parse('2020-04-01 09:00:00');
		$task->updated_at = Carbon::parse('2020-04-01 09:00:00');
		$task->save();

		$dt = Carbon::parse('2020-05-18 16:00:00');
		$task = new Task();
		$task->title = 'task 3';
		$task->description = 'task 3 unfinished';
		$task->task_group_id = 2;
		$task->status = TaskStatusEnum::InProgress;
		$task->end_time = $dt;
		$task->creater_id = 3;
		$task->created_at = Carbon::parse('2020-04-01 09:00:00');
		$task->updated_at = Carbon::parse('2020-04-01 09:00:00');
		$task->save();

		$dt = Carbon::parse('2020-05-22 16:00:00');
		$task = new Task();
		$task->title = 'task 4';
		$task->description = 'task 4 unfinished';
		$task->task_group_id = 2;
		$task->status = TaskStatusEnum::InProgress;
		$task->end_time = $dt;
		$task->creater_id = 1;
		$task->created_at = Carbon::parse('2020-04-01 09:00:00');
		$task->updated_at = Carbon::parse('2020-04-01 09:00:00');
		$task->save();

		$dt = Carbon::parse('2020-03-18 16:00:00');
		$dt_actual_end = Carbon::parse('2020-04-28 16:00:00');
		$task = new Task();
		$task->title = 'task 5';
		$task->description = 'task 5 finished overdate';
		$task->task_group_id = 2;
		$task->status = TaskStatusEnum::Done;
		$task->actual_end_time = $dt_actual_end;
		$task->end_time = $dt;
		$task->creater_id = 3;
		$task->created_at = Carbon::parse('2020-04-01 09:00:00');
		$task->updated_at = Carbon::parse('2020-04-01 09:00:00');
		$task->save();

		$dt = Carbon::parse('2020-05-18 16:00:00');
		$dt_actual_end = Carbon::parse('2020-04-10 16:00:00');
		$task = new Task();
		$task->title = 'task 6';
		$task->description = 'task 6 finished intime';
		$task->task_group_id = 2;
		$task->status = TaskStatusEnum::Done;
		$task->actual_end_time = $dt_actual_end;
		$task->end_time = $dt;
		$task->creater_id = 4;
		$task->created_at = Carbon::parse('2020-04-01 09:00:00');
		$task->updated_at = Carbon::parse('2020-04-01 09:00:00');
		$task->save();

		$dt = Carbon::parse('2020-03-18 16:00:00');
		$task = new Task();
		$task->title = 'task 7';
		$task->description = 'task 7 unfinished overdate';
		$task->task_group_id = 2;
		$task->status = TaskStatusEnum::InProgress;
		$task->end_time = $dt;
		$task->creater_id = 1;
		$task->created_at = Carbon::parse('2020-04-01 09:00:00');
		$task->updated_at = Carbon::parse('2020-04-01 09:00:00');
		$task->save();
	}
}
