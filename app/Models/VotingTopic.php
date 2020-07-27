<?php

namespace App\Models;

use App\Http\Utils\Helpers;
use Illuminate\Database\Eloquent\Model;

class VotingTopic extends Model
{
	protected $guarded = [];

	protected $hidden = ['creater_id', 'updater_id', 'created_at', 'updated_at'];

	public function notifyAllStakeHolders($connection, $message, $accountIds, $title, $data)
	{
		$dbAccountSession = new AccountSession();
		$dbAccountSession->setConnection($connection);

		Helpers::pushNotifications(
			$dbAccountSession->whereIn('account_id', $accountIds)->pluck('fcm_token')->toArray(),
			$message,
			$title,
			$data
		);
	}
}
