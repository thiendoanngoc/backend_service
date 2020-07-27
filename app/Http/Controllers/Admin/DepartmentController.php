<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{
	public function index()
	{
		$departments = Department::all();
		return $this->responseResult($departments);
	}

	public function show(Department $department)
	{
		return $this->responseResult($department);
	}

	public function store(Request $request)
	{
		$validated = $this->validateRequest([
			'name' => 'required|max:32|unique:departments',
			'code' => 'max:32|unique:departments'
		]);

		$department = new Department();
		$department->name = $validated['name'];
		$department->code = $validated['code'] ?? null;

		try {
			if ($department->save()) {
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

	public function update(Request $request, Department $department)
	{
		$validated = $this->validateRequest([
			'name' => 'required|max:32|unique:departments,name,' . $department->id,
			'code' => 'max:32|unique:departments'
		]);

		$department->name = $validated['name'];
		$department->code = $validated['code'] ?? null;

		try {
			if ($department->save()) {
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

	public function destroy(Department $department)
	{
		try {
			if ($department->delete()) {
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
