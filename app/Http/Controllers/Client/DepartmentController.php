<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Department;

class DepartmentController extends Controller
{
	public function index()
	{
		$departments = Department::all();
		return $this->responseResult($departments);
	}

	public function getPositions(Department $department)
	{
		$positions = $department->positions;
		return $this->responseResult($positions);
	}
}
