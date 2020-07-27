<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Attachment;
use Exception;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{
	public function index()
	{
		$announcements = Announcement::all();

		try {
			foreach ($announcements as $item) {
				$id = $item->id;
				$attachments = Attachment::join('announcement_attachments', 'attachment_id', '=', 'id')
					->where('announcement_id', $id)
					->select('path')
					->get()
					->toArray();
	
				$item['attachments'] = $attachments;
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
		
		return $this->responseResult($announcements);
	}

	public function show(Announcement $announcement)
	{
		$announceId = $announcement->id;
		try {
			$attachments = Attachment::join('announcement_attachments', 'attachment_id', '=', 'id')
				->where('announcement_id', $announceId)
				->select('path')
				->get()
				->toArray();
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
		
		$announcement['attachments'] = $attachments;
		return $this->responseResult($announcement);
	}
}
