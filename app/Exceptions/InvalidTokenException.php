<?php

namespace App\Exceptions;

class InvalidTokenException extends AppException
{
	public function __construct($message = null, $statusCode = 521)
	{
		$defaultError = 'E003';
		parent::__construct($message ? $message : trans($defaultError), $defaultError, $statusCode);
	}
}
