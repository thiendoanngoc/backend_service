<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Enums\NotificationTypeEnum;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
	{
		$notis = Notification::where('account_id', $this->jwtAccount->id)->get()->sortByDesc('updated_at')->values();
		return $this->responseResult($notis);
	}

	public function readNotification(Request $request)
	{
		$notiId = $request->input('notification_id');

		$success = false;
		$noti = Notification::where('account_id', $this->jwtAccount->id)
			->where('id', $notiId)
			->first();
		if($noti) {
			if($noti->is_read == false) {
				$noti->is_read = true;
				$success = $noti->save();
			} else {
				$success = true;
			}
		}
		return $this->responseResult(null, $success);
	}

	public function getUnreadNotification()
	{
		$result = new \stdClass();
		$notis = Notification::where('account_id', $this->jwtAccount->id)
			->where('is_read', false)
			->get()->sortByDesc('updated_at')->values();
		
		// $result->unread = $notis;
		$result->unread_count = count($notis);
		return $this->responseResult($result);
	}

	public function destroyNoti(Notification $noti)
	{
		$notiId = $noti->id;
		$account_id = $this->jwtAccount->id;
		
		DB::transaction(function () use ($notiId, $account_id) {
			try {
				Notification::where('account_id', $account_id)
					->where('id', $notiId)
					->first()
					->destroy($notiId);
			} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);
			DB::rollBack();
			return $this->responseResult(null, false);
			}
		});

		return $this->responseResult();
	}

	public function destroyAll(Notification $noti)
	{
		$notiId = $noti->id;
		$account_id = $this->jwtAccount->id;

		DB::transaction(function () use ($notiId, $account_id) {
			try {
				Notification::where('account_id', $account_id)->delete($notiId);
			} catch (Exception $e) {
				Log::error('message: ' . $e->getMessage() . ', code: ' . $e->getCode());
				Log::error($e);
				DB::rollBack();
				return $this->responseResult(null, false);
			}
		});

		return $this->responseResult();
	}
}
