<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Position;
use Exception;
use Illuminate\Support\Facades\Log;

class PositionController extends Controller
{
	public function index()
	{
		$positions = Position::all();
		return $this->responseResult($positions);
	}

	public function show(Position $position)
	{
		return $this->responseResult($position);
	}

	public function store()
	{
		$validated = $this->validateRequest([
			'department_id' => 'required|numeric',
			'name' => 'required|max:32|unique:positions',
			'level' => 'required|numeric',
		]);

		$position = new Position();
		$position->department_id = $validated['department_id'];
		$position->name = $validated['name'];
		$position->level = $validated['level'];

		try {
			if ($position->save()) {
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

	public function update(Position $position)
	{
		$validated = $this->validateRequest([
			'department_id' => 'required|numeric',
			'name' => 'required|max:32|unique:positions,name,' . $position->id,
			'level' => 'required|numeric',
		]);

		$position->department_id = $validated['department_id'];
		$position->name = $validated['name'];
		$position->level = $validated['level'];

		try {
			if ($position->save()) {
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

	public function destroy(Position $position)
	{
		try {
			if ($position->delete()) {
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
