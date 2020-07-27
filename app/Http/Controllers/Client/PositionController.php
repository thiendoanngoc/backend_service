<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Position;

class PositionController extends Controller
{
	public function index()
	{
		$positions = Position::all();
		return $this->responseResult($positions);
	}
}
