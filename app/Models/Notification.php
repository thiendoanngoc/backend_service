<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Notification extends Model
{
	protected $guarded = [];

	protected $hidden = ['created_at', 'updated_at'];

	
	public static function createNotification($receivers, $type, $refId, $title, $content)
	{
		DB::transaction(function () use ($receivers, $type, $refId, $title, $content) {
			foreach ($receivers as $receiver) {
				$noti = new Notification();
				$noti->account_id = $receiver;
				$noti->type = $type;
				$noti->ref_id = $refId;
				$noti->title = $title;
				$noti->content = $content;
				
				if (!$noti->save()) {
					Log::error('Failed to create notification');
				}
			}
		});
	}
}
