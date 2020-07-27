<?php

namespace App\Exceptions;

use App\Models\AppResponse;
use Exception;

class AppException extends Exception
{
	private $statusText;

	public function __construct($message = null, $statusText = '', $statusCode = 520)
	{
		$this->message = $message ? $message : trans($statusText);
		$this->statusText = $statusText ? trans($statusText, [], 'en') : trans('E001', [], 'en');
		$this->code = $statusCode;
	}

	public function render($request)
	{
		if (get_class($this) === 'App\Exceptions\ValidatorException') {
			$appResponse = new AppResponse(json_decode($this->message), false, $this->statusText, $this->code);
		} else {
			$appResponse = new AppResponse($this->message, false, $this->statusText, $this->code);
		}

		return response()->json($appResponse);
	}
}
