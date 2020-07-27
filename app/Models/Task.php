<?php

namespace App\Models;

use App\Enums\TaskRoleEnum;
use App\Http\Utils\Helpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $hidden = ['creater_id', 'updater_id', 'deleted_at'];

	public function taskAttachments()
	{
		return $this->hasMany(TaskAttachment::class);
	}

	public function taskAssignees()
	{
		return $this->hasMany(TaskAssignee::class);
	}

	public function creater()
	{
		return $this->belongsTo(Account::class, 'creater_id');
	}

	public function getCreatedAtAttribute($date)
	{
		return date('Y-m-d H:i:s', strtotime($date));
	}

	public function getUpdatedAtAttribute($date)
	{
		return date('Y-m-d H:i:s', strtotime($date));
	}

	public function taskGroup() {
		return $this->belongsTo(TaskGroup::class);
	}

	public function getAttachmentPaths()
	{
		$attachmentPaths = array();

		$taskAttachments = $this->taskAttachments()->get();
		foreach ($taskAttachments as $taskAttachment) {
			array_push($attachmentPaths, $taskAttachment->attachment->path);
		}

		return $attachmentPaths;
	}

	public function getAccountByTaskRole($taskRole)
	{
		$account = null;

		$taskAssignees = $this->taskAssignees()->get();
		if($taskRole === TaskRoleEnum::Supporter) {
			$account = array();
			foreach ($taskAssignees as $taskAssignee) {
				if ($taskAssignee->task_role === $taskRole) {
					array_push($account, $taskAssignee->account);
				}
			}
		} else {
			foreach ($taskAssignees as $taskAssignee) {
				if ($taskAssignee->task_role === $taskRole) {
					$account = $taskAssignee->account;
					break;
				}
			}
		}

		return $account;
	}

	public function notifyAllStakeHolders($connection, $message, $invokerId, $title, $data)
	{
		$accountIds = $this->taskAssignees->where('account_id', '!=', $invokerId)->pluck('account_id')->toArray();

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
