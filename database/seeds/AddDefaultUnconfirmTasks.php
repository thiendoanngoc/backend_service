<?php

use Carbon\Carbon;
use App\Models\UnconfirmTask;
use Illuminate\Database\Seeder;

class AddDefaultUnconfirmTasks extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dt = Carbon::parse('2020-10-29 16:00:00');
        $unconfirmTask = new UnconfirmTask();
        $unconfirmTask->task_id = 1;
        $unconfirmTask->update_assignee_id = 2;
        $unconfirmTask->reason = 'reason abc';
        $unconfirmTask->update_due_date = $dt;
        $unconfirmTask->creater_id = 1;
        $unconfirmTask->save();

        $dt = Carbon::parse('2020-10-19 16:00:00');
        $unconfirmTask = new UnconfirmTask();
        $unconfirmTask->task_id = 2;
        $unconfirmTask->update_assignee_id = 1;
        $unconfirmTask->reason = 'reason xyz';
        $unconfirmTask->update_due_date = $dt;
        $unconfirmTask->creater_id = 2;
        $unconfirmTask->save();
    }
}
