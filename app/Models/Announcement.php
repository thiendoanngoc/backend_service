<?php

namespace App\Models;

use App\Http\Utils\Helpers;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
	protected $guarded = [];

	protected $hidden = ['creater_id', 'updater_id', 'created_at', 'updated_at'];

	public function notifyToAllUser($message)
	{
		$dbAccountSession = new AccountSession();

		Helpers::pushNotifications(
			$dbAccountSession->all()->pluck('fcm_token')->toArray(),
			$message
		);
	}
}
