<?php

namespace App\Exceptions;

class SessionExpiredException extends AppException
{
	public function __construct($message = null, $statusCode = 523)
	{
		$defaultError = 'E007';
		parent::__construct($message ? $message : trans($defaultError), $defaultError, $statusCode);
	}
}
