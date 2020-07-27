<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $primaryKey = ['role_id', 'web_route_id'];

	protected $hidden = ['deleted_at'];

	public $incrementing = false;

	public $timestamps = false;

	public function webRoute()
	{
		return $this->belongsTo(WebRoute::class);
	}
}
