<?php

use App\Models\Position;
use Illuminate\Database\Seeder;

class AddDefaultPositions extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$position = new Position(); // 1
		$position->department_id = 1;
		$position->name = 'Tổng Giám Đốc';
		$position->level = 1;
		$position->save();

		$position = new Position(); // 2
		$position->department_id = 2;
		$position->name = 'Chánh Văn Phòng';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 3
		$position->department_id = 3;
		$position->name = 'Trưởng Ban Thư ký';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 4
		$position->department_id = 4;
		$position->name = 'Trưởng Ban TCNS';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 5
		$position->department_id = 5;
		$position->name = 'Trưởng Ban TCKT';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 6
		$position->department_id = 6;
		$position->name = 'Trưởng Ban KHĐT';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 7
		$position->department_id = 7;
		$position->name = 'Trưởng Ban ATCL';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 8
		$position->department_id = 8;
		$position->name = 'Trưởng Ban TM';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 9
		$position->department_id = 9;
		$position->name = 'Trưởng Ban KTSX';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 10
		$position->department_id = 10;
		$position->name = 'Trưởng Ban Công nghiệp';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 11
		$position->department_id = 11;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 12
		$position->department_id = 12;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 13
		$position->department_id = 13;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 14
		$position->department_id = 14;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 15
		$position->department_id = 15;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 16
		$position->department_id = 16;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 17
		$position->department_id = 17;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 18
		$position->department_id = 18;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 19
		$position->department_id = 19;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 20
		$position->department_id = 20;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 21
		$position->department_id = 21;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 22
		$position->department_id = 22;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 23
		$position->department_id = 23;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 24
		$position->department_id = 24;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 25
		$position->department_id = 25;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 26
		$position->department_id = 26;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 27
		$position->department_id = 27;
		$position->name = 'Giám đốc';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 28
		$position->department_id = 28;
		$position->name = 'Project Manager';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 29
		$position->department_id = 29;
		$position->name = 'Project Manager ';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 30
		$position->department_id = 30;
		$position->name = 'Project Manager ';
		$position->level = 2;
		$position->save();

		$position = new Position(); // 31
		$position->department_id = 2;
		$position->name = 'Phó Chánh VP';
		$position->level = 3;
		$position->save();

		$position = new Position(); // 32
		$position->department_id = 2;
		$position->name = 'Trưởng Phòng VTLT';
		$position->level = 3;
		$position->save();

		$position = new Position(); // 33
		$position->department_id = 2;
		$position->name = 'Trưởng Phòng QTHC';
		$position->level = 3;
		$position->save();

		$position = new Position(); // 34
		$position->department_id = 2;
		$position->name = 'Phó Trưởng phòng TKTH';
		$position->level = 3;
		$position->save();

		$position = new Position(); // 35
		$position->department_id = 2;
		$position->name = 'Phó Trưởng Phòng CNTT';
		$position->level = 3;
		$position->save();

		$position = new Position(); // 36
		$position->department_id = 2;
		$position->name = 'Thư ký Ban TGĐ';
		$position->level = 3;
		$position->save();

		$position = new Position(); // 37
		$position->department_id = 2;
		$position->name = 'Trưởng nhóm phòng TT & PR';
		$position->level = 3;
		$position->save();

		$position = new Position(); // 38
		$position->department_id = 2;
		$position->name = 'Chuyên viên phòng CNTT';
		$position->level = 4;
		$position->save();

		$position = new Position(); // 39
		$position->department_id = 2;
		$position->name = 'Chuyên viên phòng VTLT';
		$position->level = 4;
		$position->save();

		$position = new Position(); // 40
		$position->department_id = 2;
		$position->name = 'Chuyên viên Tổng hợp';
		$position->level = 4;
		$position->save();

		$position = new Position(); // 41
		$position->department_id = 2;
		$position->name = 'Chuyên viên phòng QTHC';
		$position->level = 4;
		$position->save();

		$position = new Position(); // 42
		$position->department_id = 2;
		$position->name = 'Nhân viên Lễ tân';
		$position->level = 4;
		$position->save();

		$position = new Position(); // 43
		$position->department_id = 2;
		$position->name = 'Nhân viên lái xe';
		$position->level = 4;
		$position->save();
	}
}
