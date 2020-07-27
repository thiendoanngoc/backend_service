<?php

namespace App\Models;

use App\Http\Utils\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $hidden = ['creater_id', 'updater_id', 'created_at', 'updated_at', 'deleted_at'];

	public function notifyToCreater($connection, $createrId, $message)
	{
		$dbAccountSession = new AccountSession();
		$dbAccountSession->setConnection($connection);

		Helpers::pushNotifications(
			$dbAccountSession->where('account_id', $createrId)->pluck('fcm_token'),
			$message
		);
	}
}
