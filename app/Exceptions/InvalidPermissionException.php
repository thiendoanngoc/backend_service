<?php

namespace App\Exceptions;

class InvalidPermissionException extends AppException
{
	public function __construct($message = null, $statusCode = 522)
	{
		$defaultError = 'E004';
		parent::__construct($message ? $message : trans($defaultError), $defaultError, $statusCode);
	}
}
