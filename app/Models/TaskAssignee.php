<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAssignee extends Model
{
	protected $guarded = [];

	protected $primaryKey = ['task_id', 'account_id'];

	public $incrementing = false;

	public $timestamps = false;

	public function account()
	{
		return $this->belongsTo(Account::class);
	}
}
