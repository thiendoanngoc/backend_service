<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $hidden = ['password', 'creater_id', 'updater_id', 'created_at', 'updated_at', 'deleted_at'];

	public function roleMappings()
	{
		return $this->hasMany(RoleMapping::class);
	}

	public function roleIds()
	{
		return $this->roleMappings->pluck('role_id')->all();
	}

	public function staff()
	{
		return $this->hasOne(Staff::class);
	}

	public function customer()
	{
		return $this->hasOne(Customer::class);
	}

	public function allMyTasks()
	{
		return $this->hasMany(TaskAssignee::class);
	}
}
