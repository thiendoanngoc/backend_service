<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
	protected $guarded = [];

	protected $hidden = ['updater_id', 'created_at', 'updated_at'];
}
