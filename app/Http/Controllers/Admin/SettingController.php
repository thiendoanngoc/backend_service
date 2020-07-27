<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;
use App\Models\Setting;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingController extends Controller
{
	public function show()
	{
		$setting = Setting::first();
		return $this->responseResult($setting);
	}

	public function update(Request $request, Setting $setting)
	{
		$validated = $this->validateRequest([
			'website_name' => 'required',
			'website_slogan' => 'required',
			'website_owner' => 'required',
			'email' => 'required|email:rfc,dns|max:32',
			'phone_number' => 'required',
			'address' => 'required',
			'working_hours' => 'required',
			'location' => '',
			'logo' => ''
		]);

		try {
			$result = DB::transaction(function () use ($setting, $validated) {
				$isError = false;

				$setting = Setting::first();
				$oldLogo = $setting->logo;

				$setting->website_name = $validated['website_name'];
				$setting->website_slogan = $validated['website_slogan'];
				$setting->website_owner = $validated['website_owner'];
				$setting->email = $validated['email'];
				$setting->phone_number = $validated['phone_number'];
				$setting->address = $validated['address'];
				$setting->working_hours = $validated['working_hours'];
				$setting->location = $validated['location'];
				$setting->logo = $validated['logo'];
				$setting->updater_id = $this->jwtAccount->id;

				$isError = !$setting->save();
				if ($isError) {
					DB::rollBack();
					Helpers::deleteImages(array($validated['logo']), false);
				} else {
					Helpers::deleteImages(array($oldLogo), false);
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
