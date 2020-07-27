<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\InvalidCompanyException;
use App\Exceptions\RequestOTPException;
use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;
use App\Models\Account;
use App\Models\AccountSession;
use App\Models\PhoneOtp;
use App\Models\Staff;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
	public function login()
	{
		$validated = $this->validateRequest([
			'username' => 'required|max:32',
			'password' => 'required|max:32',
			'device_name' => 'required',
			'device_platform' => 'required',
			'device_imei' => 'required',
			'remember' => ''
		]);

		$query = Account::query();
		$query = $query->where(function ($query) use ($validated) {
			$query->orWhere('username', strtolower($validated['username']))
				->orWhere('phone_number', $validated['username'])
				->orWhere('email', strtolower($validated['username']));
		});
		$account = $query->where('password', hash('sha256', $validated['password']))->first();

		$jwt = '';
		if ($account) {
			// Only allow 1 logged session
			$accountSessions = AccountSession::where('account_id', $account->id)->get();
			foreach ($accountSessions as $accountSession) {
				$accountSession->delete();
			}

			$accountSession = new AccountSession();
			$accountSession->account_id = $account->id;
			$accountSession->device_name = $validated['device_name'];
			$accountSession->device_platform = $validated['device_platform'];
			$accountSession->device_imei = $validated['device_imei'];
			$accountSession->is_remember = $validated['remember'] ?? false;
			$accountSession->save();

			$jwt = Helpers::getJWT($account->id, $accountSession->id);
		} else {
			return $this->responseResult(trans('E006'), false);
		}

		$data = new \stdClass();
		$data->company = $this->connection;
		$data->jwt = $jwt;
		$data->id = $account->id;
		$data->username = $account->username;
		$data->full_name = $account->full_name;
		$data->image = $account->image;

		return $this->responseResult($data);
	}

	public function loginByPhone()
	{
		$validated = $this->validateRequest([
			'phone_number' => 'required|max:32',
			'otp_code' => 'required|max:8',
			'device_name' => 'required',
			'device_platform' => 'required',
			'device_imei' => 'required',
			'remember' => ''
		]);

		$masterDBAccount = new Account();
		$masterDBAccount->setConnection(config('app.master_db'));
		$masterDBAccount = $masterDBAccount->where('phone_number', $validated['phone_number'])->first();

		if ($masterDBAccount) {
			$this->switchDBConnection($masterDBAccount->db_name);
		} else {
			return $this->responseResult(trans('E011'), false);
		}

		$phoneOtp = PhoneOtp::where('phone_number', $validated['phone_number'])->first();

		$jwt = '';
		if ($phoneOtp) {
			$currentTime = date('Y-m-d H:i:s');
			$after1Minute = date('Y-m-d H:i:s', strtotime('+1 minute', strtotime($phoneOtp->created_at)));

			if ($currentTime >= $phoneOtp->created_at && $currentTime < $after1Minute) {
				if ($phoneOtp->otp_code === $validated['otp_code']) {
					$phoneOtp->delete();
				} else {
					return $this->responseResult(trans('E011'), false);
				}
			} else {
				return $this->responseResult(trans('E011'), false);
			}

			$account = Account::where('phone_number', $validated['phone_number'])->first();
			if ($account) {
				// Only allow 1 logged session
				$accountSessions = AccountSession::where('account_id', $account->id)->get();
				foreach ($accountSessions as $accountSession) {
					$accountSession->delete();
				}

				$accountSession = new AccountSession();
				$accountSession->account_id = $account->id;
				$accountSession->device_name = $validated['device_name'];
				$accountSession->device_platform = $validated['device_platform'];
				$accountSession->device_imei = $validated['device_imei'];
				$accountSession->is_remember = $validated['remember'] ?? false;
				$accountSession->save();

				$jwt = Helpers::getJWT($account->id, $accountSession->id);
			} else {
				return $this->responseResult(trans('E011'), false);
			}
		} else {
			return $this->responseResult(trans('E011'), false);
		}

		$data = new \stdClass();
		$data->company = $this->connection;
		$data->jwt = $jwt;
		$data->id = $account->id;
		$data->username = $account->username;
		$data->full_name = $account->full_name;
		$data->image = $account->image;

		return $this->responseResult($data);
	}

	public function register()
	{
		$validated = $this->validateRequest([
			'position_id' => 'required',
			'username' => 'required|max:32|unique:accounts',
			'email' => 'required|email:rfc,dns|max:32|unique:accounts',
			'phone_number' => 'required|max:32|unique:accounts',
			'password' => 'required|max:32'
		]);

		if ($this->connection === config('app.master_db')) {
			// prevent register user in master db
			throw new InvalidCompanyException();
		}

		$account = new Account();
		$account->username = strtolower($validated['username']);
		$account->phone_number = $validated['phone_number'];
		$account->email = strtolower($validated['email']);
		$account->password = hash('sha256', $validated['password']);

		try {
			$company = $this->connection;
			$result = DB::transaction(function () use ($account, $validated, $company) {
				$isError = !$account->save();

				if (!$isError) {
					$masterDBAccount = new Account();
					$masterDBAccount->setConnection(config('app.master_db'));
					$masterDBAccount->username = $account->username;
					$masterDBAccount->phone_number = $account->phone_number;
					$masterDBAccount->email = $account->email;
					$masterDBAccount->db_name = $company;

					$isError = !$masterDBAccount->save();

					if (!$isError) {
						$staff = new Staff();
						$staff->account_id = $account->id;
						$staff->position_id = $validated['position_id'];
						$isError = !$staff->save();
					}

					if ($isError) {
						DB::rollBack();
					}
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

	public function logout()
	{
		$accountSession = Helpers::getAccountSession(request()->header('jwt'));
		if ($accountSession) {
			AccountSession::destroy($accountSession->id);
		}

		return $this->responseResult();
	}

	public function requestOTPCode()
	{
		$validated = $this->validateRequest([
			'phone_number' => 'required|max:32',
		]);

		$masterDBAccount = new Account();
		$masterDBAccount->setConnection(config('app.master_db'));
		$masterDBAccount = $masterDBAccount->where('phone_number', $validated['phone_number'])->first();

		if ($masterDBAccount) {
			$this->switchDBConnection($masterDBAccount->db_name);
		} else {
			return $this->responseResult(trans('E006'), false);
		}

		$otpCode = Helpers::getRandomStringNumber();
		$phoneOtp = PhoneOtp::where('phone_number', $validated['phone_number'])->first();
		if ($phoneOtp) {
			$currentTime = date('Y-m-d H:i:s');
			$after1Minute = date('Y-m-d H:i:s', strtotime('+1 minute', strtotime($phoneOtp->created_at)));

			if ($currentTime >= $phoneOtp->created_at && $currentTime < $after1Minute) {
				throw new RequestOTPException();
			} else {
				$phoneOtp->otp_code = $otpCode;
				$phoneOtp->created_at = date('Y-m-d H:i:s');
			}
		} else {
			$phoneOtp = new PhoneOtp();
			$phoneOtp->phone_number = $validated['phone_number'];
			$phoneOtp->otp_code = $otpCode;
		}

		try {
			if ($phoneOtp->save()) {
				Helpers::sendOTPCode($phoneOtp->phone_number, $phoneOtp->otp_code);
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

	public function updateFCMToken()
	{
		$validated = $this->validateRequest([
			'fcm_token' => 'required',
		]);

		$accountSession = Helpers::getAccountSession(request()->header('jwt'));
		if (!$accountSession) {
			return $this->responseResult(null, false);
		}
		$accountSession->fcm_token = $validated['fcm_token'];

		try {
			if ($accountSession->save()) {
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
