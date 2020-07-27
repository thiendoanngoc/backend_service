<?php

namespace App\Exceptions;

class InvalidCompanyException extends AppException
{
	public function __construct($message = null, $statusCode = 525)
	{
		$defaultError = 'E005';
		parent::__construct($message ? $message : trans($defaultError), $defaultError, $statusCode);
	}
}
