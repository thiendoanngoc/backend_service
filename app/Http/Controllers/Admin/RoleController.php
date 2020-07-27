<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleEnum;
use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
	public function index()
	{
		$roles = Role::where('id', '!=', RoleEnum::Administrator)
			->orderBy('id', 'desc')->get();

		return $this->responseResult($roles);
	}

	public function show(Role $role)
	{
		if ($role->id === RoleEnum::Administrator) {
			return $this->responseResult(null, false);
		}

		$role->webRoutes = $role->webRoutes();
		return $this->responseResult($role);
	}

	public function store(Request $request)
	{
		$validated = $this->validateRequest([
			'role_name' => 'required|max:32|unique:roles',
			'web_route_ids' => 'required|array'
		]);

		try {
			$result = DB::transaction(function () use ($validated) {
				$isError = false;

				$role = new Role();
				$role->role_name = $validated['role_name'];
				$role->creater_id = $this->jwtAccount->id;
				$isError |= !$role->save();

				// Default view home admin page permission
				$permission = new Permission();
				$permission->role_id = $role->id;
				$permission->web_route_id = 1;
				$isError |= !$permission->save();

				foreach ($validated['web_route_ids'] as $webRouteId) {
					$permission = new Permission();
					$permission->role_id = $role->id;
					$permission->web_route_id = $webRouteId;
					$isError |= !$permission->save();
				}

				if ($isError) {
					DB::rollBack();
				}

				return !$isError;
			});

			if ($result) {
				return $this->responseResult();
			} else {
				return $this->responseResult(null, false);
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
	}

	public function update(Request $request, Role $role)
	{
		if ($role->id === RoleEnum::Administrator) {
			return $this->responseResult(null, false);
		}

		$validated = $this->validateRequest([
			'role_name' => 'required|max:32|unique:roles,role_name,' . $role->id,
			'web_route_ids' => 'required|array'
		]);

		try {
			$role->role_name = $validated['role_name'];

			$result = DB::transaction(function () use ($role, $validated) {
				$isError = !$role->save();

				$isError |= !DB::statement('delete from permissions where role_id = ' . $role->id);

				// Default view home admin page permission
				$permission = new Permission();
				$permission->role_id = $role->id;
				$permission->web_route_id = 1;
				$isError |= !$permission->save();

				foreach ($validated['web_route_ids'] as $webRouteId) {
					$permission = new Permission();
					$permission->role_id = $role->id;
					$permission->web_route_id = $webRouteId;
					$isError |= !$permission->save();
				}

				if ($isError) {
					DB::rollBack();
				}

				return !$isError;
			});

			if ($result) {
				return $this->responseResult();
			} else {
				return $this->responseResult(null, false);
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
	}

	public function destroy(Role $role)
	{
		try {
			if ($role->id === RoleEnum::Administrator) {
				return $this->responseResult(trans('E009'), false);
			} else {
				$result = DB::transaction(function () use ($role) {
					$isError = false;
					// $isError |= !DB::statement('delete from role_mappings where role_id = ' . $role->id);
					// $isError |= !DB::statement('delete from permissions where role_id = ' . $role->id);
					$isError |= !DB::statement('update role_mappings set deleted_at = \'' . date('Y-m-d H:i:s') . '\' where role_id = ' . $role->id);

					$role->updater_id = $this->jwtAccount->id;
					$isError |= !$role->save();
					$isError |= !$role->delete();

					if ($isError) {
						DB::rollBack();
					}

					return !$isError;
				});

				if ($result) {
					return $this->responseResult();
				} else {
					return $this->responseResult(null, false);
				}
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
	}
}
