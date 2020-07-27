<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TaskGroup;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TaskGroupController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		$taskGroups = TaskGroup::all();

		return $this->responseResult($taskGroups);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\TaskGroup  $taskGroup
	 * @return \Illuminate\Http\Response
	 */
	public function show(TaskGroup $taskGroup)
	{
		return $this->responseResult($taskGroup);
	}

	public function store(Request $request)
	{
		$validated = $this->validateRequest([
			'name' => 'required|unique:task_groups,name',
			'description' => '',
			'note' => ''
		]);

		$group = new TaskGroup();
		$group->name = $validated['name'];
		$group->description = $validated['description'] ?? null;
		$group->note = $validated['note'] ?? null;
		$group->creater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($group) {
				if (!$group->save()) {
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

	public function update(TaskGroup $taskGroup)
	{
		$validated = $this->validateRequest([
			'name' => 'required|unique:task_groups,name,' . $taskGroup->id,
			'description' => '',
			'note' => ''
		]);

		$taskGroup->name = $validated['name'];
		$taskGroup->description = $validated['description'] ?? null;
		$taskGroup->note = $validated['note'] ?? null;
		$taskGroup->updater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($taskGroup) {
				if (!$taskGroup->save()) {
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
}
