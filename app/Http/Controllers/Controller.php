<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Exceptions\InvalidCompanyException;
use App\Exceptions\ValidatorException;
use App\Http\Utils\Helpers;
use App\Models\AppResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class Controller extends BaseController
{
	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	public $connection;

	private $excludeRoutes = array(
		'auth.login-by-phone',
		'auth.request-otp-code',
		'settings.show',
	);

	private $locales = array(
		'en',
		'vi'
	);

	protected $jwtAccount;

	public function __construct()
	{
		$company = request()->header('company');
		$locale = request()->header('locale');

		if (!$this->checkDBName($company)) {
			throw new InvalidCompanyException();
		}
		$this->switchDBConnection($company);

		if ($locale && in_array($locale, $this->locales)) {
			app()->setLocale($locale);
		}

		$this->jwtAccount = Helpers::getAccountFromJWT();
	}

	protected function checkDBName($dbName)
	{
		if ($dbName) {
			if (!array_key_exists($dbName, Config::get('database')['connections'])) {
				return false;
			}
		} else {
			if (!in_array(request()->route()->getName(), $this->excludeRoutes)) {
				return false;
			}
		}

		return true;
	}

	protected function switchDBConnection($connection)
	{
		$oldConnection = config('database.default');
		Config::set('database.default', $connection);
		DB::purge($oldConnection);

		$this->connection = $connection;
	}

	public function isAdmin()
	{
		return in_array(RoleEnum::Administrator, $this->jwtAccount->roleIds());
	}

	public function responseResult($data = null, $result = true, $message = null)
	{
		$appResponse = new AppResponse($data, $result, $message);
		return response()->json($appResponse);
	}

	public function validateRequest($rules = [])
	{
		$validator = Validator::make(request()->all(), $rules);

		if ($validator->fails()) {
			$data = new \stdClass();
			$data->errors = $validator->errors()->all();
			throw new ValidatorException(json_encode($data));
		} else {
			return $validator->getData();
		}
	}
}
