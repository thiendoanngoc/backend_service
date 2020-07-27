<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;
use App\Models\Device;
use App\Models\Attachment;
use App\Enums\DeviceStatusEnum;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DeviceController extends Controller
{
	public function index()
	{
		$devices = Device::all();
		
		return $this->responseResult($devices);
	}

	public function show(Device $device)
	{
		return $this->responseResult($device);
	}

	public function store(Request $request)
	{
		$validated = $this->validateRequest([
			'account_id' => 'required|exists:accounts,id',
			'name' => 'required|max:32|unique:devices',
			'model' => 'required|max:32',
			'bought_date' => 'required|date',
			'guarantee_date' => 'required|date|after:bought_date',
			'detail' => '',
			'status' => 'required|numeric',
			'status' => Rule::in(DeviceStatusEnum::$types),
			'note' => '',
		]);

		$device = new Device();
		$device->account_id = $validated['account_id'];
		$device->name = $validated['name'];
		$device->model = $validated['model'];
		$device->bought_date = $validated['bought_date'];
		$device->guarantee_date = $validated['guarantee_date'];
		$device->detail = $validated['detail'];
		$device->status = $validated['status'];
		$device->note = $validated['note'];
		$device->creater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($device) {
				if (!$device->save()) {
					DB::rollBack();
					return $this->responseResult(null, false);
				}
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}

		return $this->responseResult();
	}

	public function update(Request $request, Device $device)
	{
		$validated = $this->validateRequest([
			'account_id' => 'required|exists:accounts,id',
			'name' => 'required|max:32|unique:devices,name,' . $device->id,
			'model' => 'required|max:32',
			'bought_date' => 'required|date',
			'guarantee_date' => 'required|date|after:bought_date',
			'detail' => '',
			'status' => 'required|numeric',
			'status' => Rule::in(DeviceStatusEnum::$types),
			'note' => '',
		]);

		$device->account_id = $validated['account_id'];
		$device->name = $validated['name'];
		$device->model = $validated['model'];
		$device->bought_date = $validated['bought_date'];
		$device->guarantee_date = $validated['guarantee_date'];
		$device->detail = $validated['detail'];
		$device->status = $validated['status'];
		$device->note = $validated['note'];
		$device->updater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($device) {
				if ($device->save()) {
					DB::rollBack();
					return $this->responseResult(null, false);
				}
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);
			return $this->responseResult(null, false);
		}
		return $this->responseResult();
	}

	public function destroy(Device $device)
	{
		$deviceId = $device->id;
		
		DB::transaction(function () use ($deviceId) {
			try {
				Device::destroy($deviceId);
			} catch (Exception $e) {
				Log::error('message: ' . $e->getMessage() . ', code: ' . $e->getCode());
				Log::error($e);
				DB::rollBack();
				return $this->responseResult(null, false);
			}
		});

		return $this->responseResult();
	}
}
