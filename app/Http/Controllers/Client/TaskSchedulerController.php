<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\TaskScheduler;

class TaskSchedulerController extends Controller
{
	public function index()
	{
		$taskSchedulers = TaskScheduler::where('account_id', $this->jwtAccount->id)->get();

		return $this->responseResult($taskSchedulers);
	}
}
