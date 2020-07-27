<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $hidden = ['creater_id', 'updater_id', 'created_at', 'updated_at', 'deleted_at'];

	public function permissions()
	{
		return $this->hasMany(Permission::class);
	}

	public function webRoutes()
	{
		return $this->permissions->pluck('web_route_id')->all();
	}
}
