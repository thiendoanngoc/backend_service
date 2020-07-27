<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
	protected $guarded = [];

	public $timestamps = false;

	public function department()
	{
		return $this->belongsTo(Department::class);
	}

	public function staffs()
	{
		return $this->hasMany(Staff::class);
	}
}
