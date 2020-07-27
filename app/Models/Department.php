<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $hidden = ['creater_id', 'updater_id', 'created_at', 'updated_at', 'deleted_at'];

	public function accounts()
	{
		$accounts = array();

		$positions = $this->positions;
		foreach ($positions as $position) {
			$staffs = $position->staffs;
			foreach ($staffs as $staff) {
				array_push($accounts, Account::find($staff->account_id));
			}
		}

		return $accounts;
	}

	public function positions()
	{
		return $this->hasMany(Position::class);
	}
}
