<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleMapping extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $primaryKey = ['account_id', 'role_id'];

	protected $hidden = ['deleted_at'];

	public $incrementing = false;

	public $timestamps = false;
}
