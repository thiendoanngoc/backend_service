<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CanteenRegistration extends Model
{
    protected $guarded = [];

    protected $hidden = ['created_at', 'updated_at', 'creater_id', 'updater_id', 'deleted_at'];

    public function register()
	{
		return $this->belongsTo(Account::class, 'account_id');
	}
}
