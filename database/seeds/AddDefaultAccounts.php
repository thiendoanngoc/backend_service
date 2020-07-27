<?php

use App\Enums\GenderEnum;
use App\Http\Utils\Helpers;
use App\Models\Account;
use Illuminate\Database\Seeder;

class AddDefaultAccounts extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// App admin default accounts

		$currentDB = config('database.default');

		if ($currentDB === config('app.master_db')) {
			$account = new Account();
			$account->db_name = $currentDB;
			$account->username = 'admin';
			$account->password = hash('sha256', '123456');
			$account->email = 'admin@abc.xyz';
			$account->phone_number = '999';
			$account->full_name = 'Admin';
			$account->save();
		}

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'admin';
		$account->password = hash('sha256', '123456');
		$account->email = 'bittosolution_admin@abc.xyz';
		$account->phone_number = '0386385429';
		$account->full_name = 'Admin';
		$account->save();

		// Excel default accounts

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'cuonglm';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh1@bittosolution.vn';
		$account->phone_number = '0358357061';
		$account->full_name = 'Lê Mạnh Cường ';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		//
		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thedh';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh2@bittosolution.vn';
		$account->phone_number = '0358357062';
		$account->full_name = 'Đỗ Huy Thế';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thuynd';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh3@bittosolution.vn';
		$account->phone_number = '0358357063';
		$account->full_name = 'Nguyễn Đức Thủy';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'chuongtn';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh4@bittosolution.vn';
		$account->phone_number = '0358357064';
		$account->full_name = 'Trần Ngọc Chương';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'baonv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh5@bittosolution.vn';
		$account->phone_number = '0358357065';
		$account->full_name = 'Nguyễn Văn Bảo';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'phongnt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh6@bittosolution.vn';
		$account->phone_number = '0358357066';
		$account->full_name = 'Nguyễn Tiên Phong';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'quantm';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh7@bittosolution.vn';
		$account->phone_number = '0358357067';
		$account->full_name = 'Trần Mạc Quân';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'namth';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh8@bittosolution.vn';
		$account->phone_number = '0358357068';
		$account->full_name = 'Trần Hoài Nam';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'khiemvt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh9@bittosolution.vn';
		$account->phone_number = '0358357069';
		$account->full_name = 'Vũ Thanh Khiêm';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thangtd';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh10@bittosolution.vn';
		$account->phone_number = '0358357070';
		$account->full_name = 'Trần Đức Thắng';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'daibv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh11@bittosolution.vn';
		$account->phone_number = '0358357071';
		$account->full_name = 'Bùi Văn Đại';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'hoangnq';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh12@bittosolution.vn';
		$account->phone_number = '0358357072';
		$account->full_name = 'Nguyễn Quốc Hoàng';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'dinhnv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh13@bittosolution.vn';
		$account->phone_number = '0358357073';
		$account->full_name = 'Nguyễn Văn Định';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thanhlt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh14@bittosolution.vn';
		$account->phone_number = '0358357074';
		$account->full_name = 'Lê Trí Thành';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'kienvv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh15@bittosolution.vn';
		$account->phone_number = '0358357075';
		$account->full_name = 'Vũ Văn Kiên';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'tuanh';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh16@bittosolution.vn';
		$account->phone_number = '0358357076';
		$account->full_name = 'Hoàng Tuấn';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'dungdt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh17@bittosolution.vn';
		$account->phone_number = '0358357077';
		$account->full_name = 'Đặng Tiến Dũng';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thangdx';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh18@bittosolution.vn';
		$account->phone_number = '0358357078';
		$account->full_name = 'Đồng Xuân Thắng';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'phuongnvn';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh19@bittosolution.vn';
		$account->phone_number = '0358357079';
		$account->full_name = 'Nguyễn Vinh Nhị Phương';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'vandh';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh20@bittosolution.vn';
		$account->phone_number = '0358357080';
		$account->full_name = 'Dương Hùng Văn';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'haint';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh21@bittosolution.vn';
		$account->phone_number = '0358357081';
		$account->full_name = 'Nguyễn Thanh Hải';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'hoainv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh22@bittosolution.vn';
		$account->phone_number = '0358357082';
		$account->full_name = 'Nguyễn Văn Hoài';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'tuantd';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh23@bittosolution.vn';
		$account->phone_number = '0358357083';
		$account->full_name = 'Trần Đình Tuấn';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'hungpv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh24@bittosolution.vn';
		$account->phone_number = '0358357084';
		$account->full_name = 'Phạm Văn Hùng ';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'phuongph';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh25@bittosolution.vn';
		$account->phone_number = '0358357085';
		$account->full_name = 'Phạm Hùng Phương';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'bangnh';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh26@bittosolution.vn';
		$account->phone_number = '0358357086';
		$account->full_name = 'Nguyễn Hải Bằng';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'vinhvk';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh27@bittosolution.vn';
		$account->phone_number = '0358357087';
		$account->full_name = 'Vũ Khánh Vinh';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'nhatnd';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh28@bittosolution.vn';
		$account->phone_number = '0358357088';
		$account->full_name = 'Nguyễn Đại Nhật';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'nambt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh29@bittosolution.vn';
		$account->phone_number = '0358357089';
		$account->full_name = 'Bùi Thanh Nam';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'dungnk';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh30@bittosolution.vn';
		$account->phone_number = '0358357090';
		$account->full_name = 'Nguyễn Khắc Dũng';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		//
		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'giangbd';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh31@bittosolution.vn';
		$account->phone_number = '0358357091';
		$account->full_name = 'Bùi Đình Giang';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thinhndv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh32@bittosolution.vn';
		$account->phone_number = '0358357092';
		$account->full_name = 'Nguyễn Thanh Tân';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'tannt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh33@bittosolution.vn';
		$account->phone_number = '0358357093';
		$account->full_name = 'Nguyễn Duy Hải';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'haind';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh34@bittosolution.vn';
		$account->phone_number = '0358357094';
		$account->full_name = 'Trương Thị Lam Giang';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'phuongtn';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh35@bittosolution.vn';
		$account->phone_number = '0358357095';
		$account->full_name = 'Trần Nam Phương';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'linhdtn';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh36@bittosolution.vn';
		$account->phone_number = '0358357096';
		$account->full_name = 'Điền Thị Ngọc Linh';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thinhtlq';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh37@bittosolution.vn';
		$account->phone_number = '0358357097';
		$account->full_name = 'Trần Lê Quốc Thịnh';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'longhp';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh38@bittosolution.vn';
		$account->phone_number = '0358357098';
		$account->full_name = 'Hà Phi Long';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thangcd';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh39@bittosolution.vn';
		$account->phone_number = '0358357099';
		$account->full_name = 'Cao Duy Thắng';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'trangnth';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh40@bittosolution.vn';
		$account->phone_number = '0358357100';
		$account->full_name = 'Nguyễn Thị Huyền Trang';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thula';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh41@bittosolution.vn';
		$account->phone_number = '0358357101';
		$account->full_name = 'Lê Anh Thư';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		//
		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'trungnm';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh42@bittosolution.vn';
		$account->phone_number = '0358357102';
		$account->full_name = 'Nguyễn Minh Trung';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'vietnq';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh43@bittosolution.vn';
		$account->phone_number = '0358357103';
		$account->full_name = 'Nguyễn Quốc Việt';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'ynhx';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh44@bittosolution.vn';
		$account->phone_number = '0358357104';
		$account->full_name = 'Nguyễn Hoàng Xuân Ý';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'hanhdt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh45@bittosolution.vn';
		$account->phone_number = '0358357105';
		$account->full_name = 'Đoàn Thị Hạnh';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'toanbq';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh46@bittosolution.vn';
		$account->phone_number = '0358357106';
		$account->full_name = 'Bùi Quốc Toàn';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'anhhtt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh47@bittosolution.vn';
		$account->phone_number = '0358357107';
		$account->full_name = 'Hồ Thị Trâm Anh';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'phuonghk';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh48@bittosolution.vn';
		$account->phone_number = '0358357108';
		$account->full_name = 'Hoàng Khánh Phương';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'tiepbd';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh49@bittosolution.vn';
		$account->phone_number = '0358357109';
		$account->full_name = 'Bùi Đức Tiệp';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'anhttd';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh50@bittosolution.vn';
		$account->phone_number = '0358357110';
		$account->full_name = 'Trần Thị Diệu Anh';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'tailv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh51@bittosolution.vn';
		$account->phone_number = '0358357111';
		$account->full_name = 'Lê Viết Tài';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'namnt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh52@bittosolution.vn';
		$account->phone_number = '0358357112';
		$account->full_name = 'Nguyễn Trọng Nam';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'tamnntt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh53@bittosolution.vn';
		$account->phone_number = '0358357113';
		$account->full_name = 'Nguyễn Thị Tố Tâm';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Female;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'vannhc';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh54@bittosolution.vn';
		$account->phone_number = '0358357114';
		$account->full_name = 'Nguyễn Hồ Cẩm Vân';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Female;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'luumt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh55@bittosolution.vn';
		$account->phone_number = '0358357115';
		$account->full_name = 'Mai Trọng Lưu';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'vutn';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh56@bittosolution.vn';
		$account->phone_number = '0358357116';
		$account->full_name = 'Trương Nguyên Vũ';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'tamnt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh57@bittosolution.vn';
		$account->phone_number = '0358357117';
		$account->full_name = 'Nhan Từ Tâm';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'maodv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh58@bittosolution.vn';
		$account->phone_number = '0358357118';
		$account->full_name = 'Đào Văn Mão';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thangph';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh59@bittosolution.vn';
		$account->phone_number = '0358357119';
		$account->full_name = 'Phạm Hồng Thắng';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'hunghv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh60@bittosolution.vn';
		$account->phone_number = '0358357120';
		$account->full_name = 'Hoàng Văn Hưng';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'ductpn';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh61@bittosolution.vn';
		$account->phone_number = '0358357121';
		$account->full_name = 'Trần Phan Nhân Đức';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'loinv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh62@bittosolution.vn';
		$account->phone_number = '0358357122';
		$account->full_name = 'Nguyễn Văn Lợi';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thuongvd';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh63@bittosolution.vn';
		$account->phone_number = '0358357123';
		$account->full_name = 'Vũ Đức Thường';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'thuanpv';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh64@bittosolution.vn';
		$account->phone_number = '0358357124';
		$account->full_name = 'Phạm Văn Thuận';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'khainc';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh65@bittosolution.vn';
		$account->phone_number = '0358357125';
		$account->full_name = 'Nguyễn Cao Khải';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'quanph';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh66@bittosolution.vn';
		$account->phone_number = '0358357126';
		$account->full_name = 'Nguyễn Hồng Quân';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();

		$account = new Account();
		if ($currentDB === config('app.master_db')) {
			$account->db_name = 'bittosolution';
		}
		$account->username = 'tuannt';
		$account->password = hash('sha256', '123456');
		$account->email = 'thinh67@bittosolution.vn';
		$account->phone_number = '0358357127';
		$account->full_name = 'Nguyễn Trung Tuấn';
		$account->birthday = '1970-01-01';
		$account->gender_id = GenderEnum::Male;
		$account->hobbies = 'Bóng đá, xem phim';
		$account->save();
	}
}
