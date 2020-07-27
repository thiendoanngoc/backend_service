<?php

use App\Models\Department;
use Illuminate\Database\Seeder;

class AddDefaultDepartments extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$department = new Department();
		$department->name = 'Ban Giám Đốc';
		$department->save();

		$department = new Department();
		$department->name = 'Văn Phòng';
		$department->code = 'VP';
		$department->save();

		$department = new Department();
		$department->name = 'Ban Thư Ký';
		$department->save();

		$department = new Department();
		$department->name = 'Ban Tổ Chức Nhân sự';
		$department->save();

		$department = new Department();
		$department->name = 'Ban Kế Toán';
		$department->save();

		$department = new Department();
		$department->name = 'Ban Kế Hoạch Và Đầu Tư';
		$department->save();

		$department = new Department();
		$department->name = 'Ban An Toàn Chất Lượng';
		$department->save();

		$department = new Department();
		$department->name = 'Ban Thương Mại';
		$department->save();

		$department = new Department();
		$department->name = 'Ban Kỹ Thuật Sản Xuất';
		$department->save();

		$department = new Department();
		$department->name = 'Ban Công Nghiệp';
		$department->save();

		$department = new Department();
		$department->name = 'PTSC Hà Nội';
		$department->save();

		$department = new Department();
		$department->name = 'PTSC Cảng Dịch Vụ';
		$department->save();

		$department = new Department();
		$department->name = 'PTSC Ban Xây Dựng';
		$department->save();

		$department = new Department();
		$department->name = 'PTSC Tàu dịch vụ';
		$department->save();

		$department = new Department();
		$department->name = 'PTSC Ban dự án Nhiệt điện I';
		$department->save();

		$department = new Department();
		$department->name = 'PTSC Cảng Dịch Vụ Quảng Bình';
		$department->save();

		$department = new Department();
		$department->name = 'PTSC Cảng Dịch Vụ Đà Nẵng';
		$department->save();

		$department = new Department();
		$department->name = 'Dịch vụ Cơ Khí Hàng Hải PTSC';
		$department->save();

		$department = new Department();
		$department->name = 'Dịch vụ khái thác dầu khí PTSC-PPS';
		$department->save();

		$department = new Department();
		$department->name = 'Lắp đặt, Vận hành, Bảo dưỡng công trình';
		$department->save();

		$department = new Department();
		$department->name = 'Dịch vụ khảo sát và công trình ngầm PTSC';
		$department->save();

		$department = new Department();
		$department->name = 'PTSC Phú Mỹ';
		$department->save();

		$department = new Department();
		$department->name = 'Khách sạn dầu khí';
		$department->save();

		$department = new Department();
		$department->name = 'PTSC Quảng Ngãi';
		$department->save();

		$department = new Department();
		$department->name = 'PTSC Thanh Hoá';
		$department->save();

		$department = new Department();
		$department->name = 'PTSC Đình Vũ';
		$department->save();

		$department = new Department();
		$department->name = 'Dịch vụ Bảo vệ An ninh dầu khí PTSC';
		$department->save();

		$department = new Department();
		$department->name = 'Ban dự án NPK, NH3';
		$department->save();

		$department = new Department();
		$department->name = 'Ban dự án GPP Cà Mau';
		$department->save();

		$department = new Department();
		$department->name = 'Ban dự án nhà máy Cảng Hải Phòng';
		$department->save();
	}
}
