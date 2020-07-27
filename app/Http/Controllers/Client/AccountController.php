<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Position;
use App\Models\Staff;
use Exception;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
	public function show()
	{
		$account = $this->jwtAccount;
		$staff = Staff::find($account->id);
		if ($staff) {
			$account->staff->position->department;
		}
		return $this->responseResult($account);
	}

	public function update()
	{
		$isChangePassword = request('isChangePassword');
		if ($isChangePassword) {
			$validated = $this->validateRequest([
				'username' => 'required|max:32',
				'email' => 'required|email:rfc,dns|max:32',
				'full_name' => 'max:32',
				'address' => 'max:128',
				'birthday' => '',
				'gender_id' => 'required',
				'current_password' => 'required|max:32',
				'password' => 'required|max:32',
				'image' => ''
			]);
		} else {
			$validated = $this->validateRequest([
				'username' => 'required|max:32',
				'email' => 'required|email:rfc,dns|max:32',
				'full_name' => 'max:32',
				'address' => 'max:128',
				'birthday' => '',
				'gender_id' => 'required',
				'image' => ''
			]);
		}

		$account = $this->jwtAccount;

		// check change password
		if ($isChangePassword) {
			$dbAccount = Account::where('username', $account->username)
				->where('password', hash('sha256', $validated['current_password']))->first();
			if (!$dbAccount) {
				return $this->responseResult(trans('validation.wrong_current_password'), false);
			} else {
				$account->password = hash('sha256', $validated['password']);
			}
		}

		// To change username, email, phone_number -> allow replace if not exist
		$dbAccount = Account::where('username', $validated['username'])->first();
		if (!$dbAccount) {
			$account->username = $validated['username'];
		}

		$dbAccount = Account::where('email', $validated['email'])->first();
		if (!$dbAccount) {
			$account->email = $validated['email'];
		}

		$account->full_name = $validated['full_name'];
		$account->address = $validated['address'];
		$account->birthday = $validated['birthday'];
		$account->gender_id = $validated['gender_id'];
		$account->image = $validated['image'];
		$account->updater_id = $this->jwtAccount->id;

		try {
			$account->save();
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}

		return $this->responseResult();
	}

	public function checkJWT()
	{
		return $this->responseResult($this->jwtAccount->id);
	}

	public function getDepartmentMembers()
	{
		$members = array();
		$staff = Staff::find($this->jwtAccount->id);
		if ($staff) {
			$currentLevel = $staff->position->level;

			if ($currentLevel === 1) {
				$positions = Position::where('level', 2)->get();
				foreach ($positions as $position) {
					$accounts = $position->department->accounts();
					foreach ($accounts as $account) {
						if ($account->staff->position->level === 2) {
							$account->staff->position->department;
							array_push($members, $account);
						}
					}
				}
			} else {
				$accounts = $this->jwtAccount->staff->position->department->accounts();
				foreach ($accounts as $account) {
					if ($account->staff->position->level === ($currentLevel + 1)) {
						$account->staff->position->department;
						array_push($members, $account);
					}
				}
			}
		}
		return $this->responseResult($members);
	}

	public function getTodayBirthday()
	{
		$day = date('d');
		$month = date('m');
		$result = Account::whereDay('birthday', '=', $day)
			->whereMonth('birthday', '=', $month)
			->get();

		return $this->responseResult($result);
	}
}
