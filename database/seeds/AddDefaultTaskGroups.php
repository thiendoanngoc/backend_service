<?php

use App\Models\TaskGroup;
use Illuminate\Database\Seeder;

class AddDefaultTaskGroups extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$taskGroup = new TaskGroup();
		$taskGroup->name = 'Công tác cải tiến';
		$taskGroup->creater_id = 1;
		$taskGroup->save();

		$taskGroup = new TaskGroup();
		$taskGroup->name = 'Hỗ trợ hội họp';
		$taskGroup->creater_id = 1;
		$taskGroup->save();

		$taskGroup = new TaskGroup();
		$taskGroup->name = 'Đưa đón nhân viên';
		$taskGroup->creater_id = 1;
		$taskGroup->save();

		$taskGroup = new TaskGroup();
		$taskGroup->name = 'Giúp việc ban giám đốc';
		$taskGroup->creater_id = 1;
		$taskGroup->save();

		$taskGroup = new TaskGroup();
		$taskGroup->name = 'Công tác chuyên môn';
		$taskGroup->creater_id = 1;
		$taskGroup->save();
	}
}
