<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;
use App\Models\Account;
use App\Models\RoleMapping;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AccountController extends Controller
{
	public function index()
	{
		$query = Account::leftJoin('role_mappings', 'role_mappings.account_id', '=', 'accounts.id');
		if (!$this->isAdmin()) {
			$query = $query->where(function ($query) {
				$query->orWhere('role_mappings.role_id', '!=', RoleEnum::Administrator)
					->orWhere('role_mappings.role_id', null);
			});
		}
		$accountIds = $query->pluck('accounts.id')->toArray();

		$accounts = Account::whereIn('id', $accountIds)
			->orderBy('id', 'desc')->get();

		return $this->responseResult($accounts);
	}

	public function show(Account $account)
	{
		if (
			in_array(RoleEnum::Administrator, $account->roleIds()) &&
			!$this->isAdmin()
		) {
			return $this->responseResult(null, false);
		}

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
			'role_ids' => 'required|array',
			'gender_id' => 'required',
			'password' => 'required|max:32',
			'image' => '',
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

				// Update role
				foreach ($validated['role_ids'] as $roleId) {
					if ($roleId == RoleEnum::Administrator && !$this->isAdmin()) {
						continue;
					}

					$roleMapping = new RoleMapping();
					$roleMapping->account_id = $account->id;
					$roleMapping->role_id = $roleId;
					$isError |= !$roleMapping->save();
				}

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
		if (
			in_array(RoleEnum::Administrator, $account->roleIds()) &&
			!$this->isAdmin()
		) {
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
				'role_ids' => 'required|array',
				'gender_id' => 'required',
				'password' => 'required|max:32',
				'image' => '',
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
				'role_ids' => 'required|array',
				'gender_id' => 'required',
				'image' => '',
				'db_name' => 'max:32'
			]);
		}

		try {
			$result = DB::transaction(function () use ($validated, $account, $isChangePassword) {
				$isError = false;

				$oldUsername = $account->username;
				$oldAccountImage = $account->image;

				// Update role
				$isError |= !DB::statement('delete from role_mappings where account_id = ' . $account->id);
				foreach ($validated['role_ids'] as $roleId) {
					if ($roleId == RoleEnum::Administrator && !$this->isAdmin()) {
						continue;
					}

					$roleMapping = new RoleMapping();
					$roleMapping->account_id = $account->id;
					$roleMapping->role_id = $roleId;
					$isError |= !$roleMapping->save();
				}

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
		if (
			in_array(RoleEnum::Administrator, $account->roleIds()) &&
			!$this->isAdmin()
		) {
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

	public function showProfile()
	{
		$account = $this->jwtAccount;
		return $this->responseResult($account);
	}

	public function updateProfile()
	{
		$isChangePassword = request('isChangePassword');
		if ($isChangePassword) {
			$validated = $this->validateRequest([
				'username' => 'required|max:32',
				'email' => 'required|email:rfc,dns|max:32',
				'phone_number' => 'required|max:32',
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
				'phone_number' => 'required|max:32',
				'full_name' => 'max:32',
				'address' => 'max:128',
				'birthday' => '',
				'gender_id' => 'required',
				'image' => ''
			]);
		}

		$account = $this->jwtAccount;
		$oldUsername = $account->username;
		$oldAccountImage = $account->image;

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

		if (!$validated['phone_number']) {
			$account->phone_number = $validated['phone_number'];
		} else {
			$dbAccount = Account::where('phone_number', $validated['phone_number'])->first();
			if (!$dbAccount) {
				$account->phone_number = $validated['phone_number'];
			}
		}

		$account->full_name = $validated['full_name'];
		$account->address = $validated['address'];
		$account->birthday = $validated['birthday'];
		$account->gender_id = $validated['gender_id'];
		$account->image = $validated['image'];
		$account->updater_id = $this->jwtAccount->id;

		try {
			$result = DB::transaction(function () use ($account, $validated, $oldUsername, $oldAccountImage) {
				$isError = false;

				if ($account->save()) {
					$masterDBAccount = new Account();
					$masterDBAccount->setConnection(config('app.master_db'));
					$masterDBAccount = $masterDBAccount->where('username', $oldUsername)->first();

					if ($masterDBAccount) {
						$masterDBAccount->username = $account->username;
						$masterDBAccount->phone_number = $account->phone_number;
						$masterDBAccount->email = $account->email;

						$isError = !$masterDBAccount->save();
					} else {
						$isError = true;
					}
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
}
