<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;
use App\Models\Account;
use App\Models\Staff;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StaffController extends Controller
{
	public function index()
	{
		$staffIds = Staff::pluck('account_id')->toArray();
		$accounts = Account::whereIn('id', $staffIds)
			->orderBy('id', 'desc')->get();

		foreach ($accounts as $account) {
			$account->staff->position->department;
		}

		return $this->responseResult($accounts);
	}

	public function show(Account $account)
	{
		$staffIds = Staff::pluck('account_id')->toArray();
		if (!in_array($account->id, $staffIds)) {
			return $this->responseResult(null, false);
		}

		$account->staff->position->department;

		return $this->responseResult($account);
	}

	public function store()
	{
		$validated = $this->validateRequest([
			'username' => 'required|max:32|unique:accounts',
			'email' => 'required|email:rfc,dns|max:32|unique:accounts',
			'phone_number' => 'required|max:32|unique:accounts',
			'full_name' => 'max:32',
			'address' => 'max:128',
			'birthday' => '',
			'gender_id' => 'required',
			'password' => 'required|max:32',
			'image' => '',
			'position_id' => 'required',
			'db_name' => 'max:32'
		]);

		try {
			$result = DB::transaction(function () use ($validated) {
				$isError = false;

				$account = new Account();
				$account->username = strtolower($validated['username']);
				$account->password = hash('sha256', $validated['password']);
				$account->email = strtolower($validated['email']);
				$account->phone_number = $validated['phone_number'];
				$account->full_name = $validated['full_name'];
				$account->address = $validated['address'];
				$account->birthday = $validated['birthday'];
				$account->gender_id = $validated['gender_id'];
				$account->image = $validated['image'];
				$account->creater_id = $this->jwtAccount->id;

				$isError |= !$account->save();

				if (!$isError) {
					$masterDBAccount = new Account();
					$masterDBAccount->setConnection(config('app.master_db'));
					$masterDBAccount->username = $account->username;
					$masterDBAccount->phone_number = $account->phone_number;
					$masterDBAccount->email = $account->email;

					if (($validated['db_name'] ?? null) && $this->jwtAccount->db_name === config('app.master_db')) {
						if ($this->checkDBName($validated['db_name'])) {
							$masterDBAccount->db_name = $validated['db_name'];
						} else {
							$isError = true;
							Log::error('message: DB name: ' . $validated['db_name'] . ' is not exists!');
						}
					} else {
						$masterDBAccount->db_name = config('database.default');
					}

					if (!$isError) {
						$isError = !$masterDBAccount->save();
					}
				}

				if (!$isError) {
					$staff = new Staff();
					$staff->account_id = $account->id;
					$staff->position_id = $validated['position_id'];
					$isError = !$staff->save();
				}

				if ($isError) {
					DB::rollBack();
					Helpers::deleteImages(array($validated['image']), false);
				}

				return !$isError;
			});

			if ($result) {
				return $this->responseResult();
			} else {
				return $this->responseResult(null, false);
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
	}

	public function update(Account $account)
	{
		$staffIds = Staff::pluck('account_id')->toArray();
		if (!in_array($account->id, $staffIds)) {
			return $this->responseResult(null, false);
		}

		$isChangePassword = request('isChangePassword');
		if ($isChangePassword) {
			$validated = $this->validateRequest([
				'username' => 'required|max:32|unique:accounts,username,' . $account->id,
				'email' => 'required|email:rfc,dns|max:32|unique:accounts,email,' . $account->id,
				'phone_number' => 'required|max:32|unique:accounts,phone_number,' . $account->id,
				'full_name' => 'max:32',
				'address' => 'max:128',
				'birthday' => '',
				'gender_id' => 'required',
				'password' => 'required|max:32',
				'image' => '',
				'position_id' => 'required',
				'db_name' => 'max:32'
			]);
		} else {
			$validated = $this->validateRequest([
				'username' => 'required|max:32|unique:accounts,username,' . $account->id,
				'email' => 'required|email:rfc,dns|max:32|unique:accounts,email,' . $account->id,
				'phone_number' => 'required|max:32|unique:accounts,phone_number,' . $account->id,
				'full_name' => 'max:32',
				'address' => 'max:128',
				'birthday' => '',
				'gender_id' => 'required',
				'image' => '',
				'position_id' => 'required',
				'db_name' => 'max:32'
			]);
		}

		try {
			$result = DB::transaction(function () use ($validated, $account, $isChangePassword) {
				$isError = false;

				$oldUsername = $account->username;
				$oldAccountImage = $account->image;

				if ($isChangePassword) {
					$account->password = hash('sha256', $validated['password']);
				}
				$account->username = strtolower($validated['username']);
				$account->email = strtolower($validated['email']);
				$account->phone_number = $validated['phone_number'];
				$account->full_name = $validated['full_name'];
				$account->address = $validated['address'];
				$account->birthday = $validated['birthday'];
				$account->gender_id = $validated['gender_id'];
				$account->image = $validated['image'];
				$account->updater_id = $this->jwtAccount->id;

				$isError |= !$account->save();

				if (!$isError) {
					$masterDBAccount = new Account();
					$masterDBAccount->setConnection(config('app.master_db'));
					$masterDBAccount = $masterDBAccount->where('username', $oldUsername)->first();

					if ($masterDBAccount) {
						$masterDBAccount->username = $account->username;
						$masterDBAccount->phone_number = $account->phone_number;
						$masterDBAccount->email = $account->email;

						if (($validated['db_name'] ?? null) && $this->jwtAccount->db_name === config('app.master_db')) {
							if ($this->checkDBName($validated['db_name'])) {
								$masterDBAccount->db_name = $validated['db_name'];
							} else {
								$isError = true;
								Log::error('message: DB name: ' . $validated['db_name'] . ' is not exists!');
							}
						} else {
							$masterDBAccount->db_name = config('database.default');
						}

						if (!$isError) {
							$isError = !$masterDBAccount->save();
						}
					} else {
						$isError = true;
					}
				}

				if (!$isError) {
					$staff = Staff::find($account->id);
					$staff->position_id = $validated['position_id'];
					$isError = !$staff->save();
				}

				if ($isError) {
					DB::rollBack();
					Helpers::deleteImages(array($validated['image']), false);
				} else {
					Helpers::deleteImages(array($oldAccountImage), false);
				}

				return !$isError;
			});

			if ($result) {
				return $this->responseResult();
			} else {
				return $this->responseResult(null, false);
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
	}

	public function destroy(Account $account)
	{
		$staffIds = Staff::pluck('account_id')->toArray();
		if (!in_array($account->id, $staffIds)) {
			return $this->responseResult(null, false);
		}

		try {
			if ($account->id !== $this->jwtAccount->id) {
				$result = DB::transaction(function () use ($account) {
					$isError = false;

					$account->updater_id = $this->jwtAccount->id;
					if ($account->save()) {
						if ($account->delete()) {
							$masterDBAccount = new Account();
							$masterDBAccount->setConnection(config('app.master_db'));
							$masterDBAccount = $masterDBAccount->where('username', $account->username)->first();

							if ($masterDBAccount) {
								if (!$masterDBAccount->delete()) {
									$isError = true;
								}
							} else {
								$isError = true;
							}
						}
					}

					if ($isError) {
						DB::rollBack();
					}

					return !$isError;
				});

				if ($result) {
					return $this->responseResult();
				} else {
					return $this->responseResult(null, false);
				}
			} else {
				return $this->responseResult(trans('E009'), false);
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
	}
}
