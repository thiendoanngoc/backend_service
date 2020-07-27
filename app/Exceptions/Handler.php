<?php

namespace App\Exceptions;

use App\Models\AppResponse;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		//
	];

	/**
	 * A list of the inputs that are never flashed for validation exceptions.
	 *
	 * @var array
	 */
	protected $dontFlash = [
		'password',
		'password_confirmation',
	];

	/**
	 * Report or log an exception.
	 *
	 * @param  \Throwable  $exception
	 * @return void
	 *
	 * @throws \Exception
	 */
	public function report(Throwable $exception)
	{
		parent::report($exception);
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Throwable  $exception
	 * @return \Symfony\Component\HttpFoundation\Response
	 *
	 * @throws \Throwable
	 */
	public function render($request, Throwable $exception)
	{
		switch (true) {
			case $exception instanceof InvalidTokenException:
				break;

			case $exception instanceof InvalidPermissionException:
				break;

			case $exception instanceof SessionExpiredException:
				break;

			case $exception instanceof ValidatorException:
				break;

			case $exception instanceof InvalidCompanyException:
				break;

			case $exception instanceof RequestOTPException:
				break;

			case $exception instanceof AppException:
				break;

			default:
				$appResponse = new AppResponse(null, false);
				return response()->json($appResponse);
				break;
		}

		return parent::render($request, $exception);
	}
}
