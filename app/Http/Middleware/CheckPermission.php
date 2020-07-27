<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use App\Exceptions\InvalidPermissionException;
use App\Exceptions\InvalidTokenException;
use App\Http\Utils\Helpers;
use App\Models\Permission;
use Closure;

class CheckPermission
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
		$account = Helpers::getAccountFromJWT();
		if (!$account) {
			throw new InvalidTokenException();
		}

		$roleIds = $account->roleIds();
		if (count($roleIds)) {
			if (!in_array(RoleEnum::Administrator, $roleIds)) {
				$isValid = false;

				foreach ($roleIds as $roleId) {
					$permissions = Permission::where('role_id', $roleId)->get();

					foreach ($permissions as $permission) {
						if ($permission->webRoute->web_route_name === $request->route()->getName()) {
							$isValid = true;
							break 2;
						}
					}
				}

				if (!$isValid) {
					throw new InvalidPermissionException();
				}
			}
		} else {
			throw new InvalidPermissionException();
		}

		return $next($request);
	}
}
