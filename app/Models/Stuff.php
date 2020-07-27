<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stuff extends Model
{
	protected $guarded = [];

	protected $hidden = ['creater_id', 'updater_id', 'created_at', 'updated_at'];

	public function creater()
	{
		return $this->belongsTo(Account::class, 'seller_id');
	}
}
