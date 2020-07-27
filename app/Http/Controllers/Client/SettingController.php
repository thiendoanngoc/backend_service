<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Setting;

class SettingController extends Controller
{
	public function show()
	{
		$this->switchDBConnection(config('app.master_db'));

		return $this->responseResult(Setting::first());
	}
}
