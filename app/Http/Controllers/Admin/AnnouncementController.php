<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;
use App\Models\Announcement;
use App\Models\AnnouncementAttachment;
use App\Models\Attachment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

	public function store(Request $request)
	{
		$validated = $this->validateRequest([
			'content' => 'required',
			'attachments' => 'array|min:1'
		]);

		$attachments = $validated['attachments'];

		$announcement = new Announcement();
		$announcement->content = $validated['content'];
		$announcement->creater_id = $this->jwtAccount->id;


		try {
			DB::transaction(function () use ($announcement, $attachments) {
				$isError = !$announcement->save();

				if ($isError) {
					DB::rollBack();
					Helpers::deleteImages($attachments, false);
					return $this->responseResult(null, false);
				} else {
					foreach ($attachments as $item) {
						$attachment = new Attachment();
						$attachment->path = $item['path'];
						if(!$attachment->save()) {
							DB::rollBack();
							Helpers::deleteImages($attachments, false);
							return $this->responseResult(null, false);
						} else {
							$announceAttach = new AnnouncementAttachment();
							$announceAttach->announcement_id = $announcement->id;
							$announceAttach->attachment_id = $attachment->id;
							if(!$announceAttach->save()) {
								DB::rollBack();
								Helpers::deleteImages($attachments, false);
								return $this->responseResult(null, false);
							}
						}
					}
				}
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}

		$announcement->notifyToAllUser(trans('I005'));

		return $this->responseResult();
	}

	public function update(Request $request, Announcement $announcement)
	{
		$validated = $this->validateRequest([
			'content' => 'required',
			'attachments' => 'array|min:1'
		]);

		$announceId = $announcement->id;
		$attachments = $validated['attachments'];

		$announcement->content = $validated['content'];
		$announcement->updater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($announcement, $attachments, $announceId) {
				$isError = !$announcement->save();
				
				if ($isError) {
					DB::rollBack();
					Helpers::deleteImages($attachments, false);
					return $this->responseResult(null, false);
				} else {
					// delete attachment and relation
					try {
						$attachIds = AnnouncementAttachment::where('announcement_id', $announceId)
							->get()
							->pluck('attachment_id')
							->all();
						AnnouncementAttachment::where('announcement_id', '=', $announceId)->delete();
						Attachment::destroy($attachIds);
					} catch (Exception $e) {
						Log::error('message: ' . $e->getMessage() . ', code: ' . $e->getCode());
						Log::error($e);
						DB::rollBack();
						Helpers::deleteImages($attachments, false);
						return $this->responseResult(null, false);
					}

					// insert attachment and relation again
					foreach ($attachments as $item) {
						$attachment = new Attachment();
						$attachment->path = $item['path'];
						if(!$attachment->save()) {
							DB::rollBack();
							Helpers::deleteImages($attachments, false);
							return $this->responseResult(null, false);
						} else {
							$announceAttach = new AnnouncementAttachment();
							$announceAttach->announcement_id = $announcement->id;
							$announceAttach->attachment_id = $attachment->id;
							if(!$announceAttach->save()) {
								DB::rollBack();
								Helpers::deleteImages($attachments, false);
								return $this->responseResult(null, false);
							}
						}
					}
				}
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);
			return $this->responseResult(null, false);
		}
		return $this->responseResult();
	}

	public function destroy(Announcement $announcement)
	{
		$announceId = $announcement->id;

		$attachments = Attachment::join('announcement_attachments', 'attachment_id', '=', 'id')
			->where('announcement_id', $announceId)
			->get()
			->pluck('path')
			->all();
		
		try {
			DB::transaction(function () use ($announceId, $attachments) {
				try {
					$attachIds = AnnouncementAttachment::where('announcement_id', $announceId)
						->get()
						->pluck('attachment_id')
						->all();
					AnnouncementAttachment::where('announcement_id', '=', $announceId)->delete();
					Attachment::destroy($attachIds);
					Announcement::destroy($announceId);
					Helpers::deleteImages($attachments, false);
				} catch (Exception $e) {
					Log::error('message: ' . $e->getMessage() . ', code: ' . $e->getCode());
					Log::error($e);
					DB::rollBack();
					return $this->responseResult(null, false);
				}
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);
			return $this->responseResult(null, false);
		}

		return $this->responseResult();
	}
}
