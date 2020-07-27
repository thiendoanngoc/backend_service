<?php

namespace App\Http\Middleware;

use App\Exceptions\InvalidTokenException;
use App\Exceptions\SessionExpiredException;
use App\Http\Utils\Helpers;
use Closure;

class CheckJWT
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$jwt = $request->header('jwt');
		if ($jwt) {
			if ($accountSession = Helpers::getAccountSession($jwt)) {
				// Update last active
				$accountSession->last_active = date('Y-m-d H:i:s');
				$accountSession->save();

				return $next($request);
			} else {
				throw new SessionExpiredException();
			}
		} else {
			throw new InvalidTokenException();
		}

		return $next($request);
	}
}
