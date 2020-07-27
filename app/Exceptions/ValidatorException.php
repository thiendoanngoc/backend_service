<?php

namespace App\Exceptions;

class ValidatorException extends AppException
{
	public function __construct($message = null, $statusCode = 524)
	{
		$defaultError = 'E008';
		parent::__construct($message ? $message : trans($defaultError), $defaultError, $statusCode);
	}
}
