<?php

namespace App\Exceptions;

class RequestOTPException extends AppException
{
	public function __construct($message = null, $statusCode = 526)
	{
		$defaultError = 'E010';
		parent::__construct($message ? $message : trans($defaultError), $defaultError, $statusCode);
	}
}
