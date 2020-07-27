<?php

namespace App\Http\Controllers\Client;

use App\Enums\NotificationTypeEnum;
use App\Enums\TaskRoleEnum;
use App\Enums\TaskStatusEnum;
use App\Models\TaskAssignee;
use App\Models\Task;
use App\Models\UnconfirmTask;
use App\Models\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UnconfirmTaskController extends Controller
{

    public function index()
	{
		$unconfirmTasks = UnconfirmTask::all();
		
		return $this->responseResult($unconfirmTasks);
	}

	public function show(UnconfirmTask $unconfirmTask)
	{
		return $this->responseResult($unconfirmTask);
	}
    
	public function requestCancel(Request $request) {
		$taskId = $request->input('task_id');
		$reason = $request->input('reason');

		$success = false;
        $result = new \stdClass();
        
        if(empty($taskId) || empty($reason)) {
            $result->success = false;
			return $this->responseResult($result);
        }

		$accountId = $this->jwtAccount->id;
		$task = Task::find($taskId);
		$hosterId = TaskAssignee::where('task_id', $task->id)
				->where('task_role', TaskRoleEnum::Hoster)
				->pluck('account_id')
				->first();
		$unconfirm = UnconfirmTask::where('task_id', $taskId)->first();
		if(!empty($task) && $accountId == $hosterId) {
			if(empty($unconfirm)) {
				$unconfirm = new UnconfirmTask();
			}
			try {
				$res = DB::transaction(function () use ($task, $unconfirm, $accountId, $reason) {
					$unconfirm->task_id = $task->id;
					$unconfirm->update_assignee_id = $accountId;
					$unconfirm->reason = $reason;
					$unconfirm->update_due_date = $task->end_time;
					$unconfirm->status = TaskStatusEnum::Reject;
					$unconfirm->creater_id = $accountId;
					
					return $unconfirm->save();
				});
				$success = $res;
			} catch (Exception $ex) {
				Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
				Log::error($ex);
				$result->success = false;
				return $this->responseResult($result);
			}
		}

		$result->success = $success;

		return $this->responseResult($result);
    }
    
    public function approveCancel(Request $request) {
        $taskId = $request->input('task_id');

		$accountId = $this->jwtAccount->id;
		$success = false;

		$task = Task::find($taskId);
		if(!empty($task)) {
			$unconfirmTask = UnconfirmTask::where('task_id', $task->id)->first();
			if($task->creater_id == $accountId && !empty($unconfirmTask)) {
				$task->status = TaskStatusEnum::Cancel;
				$task->note = $unconfirmTask->reason;
				
				$success = DB::transaction(function () use ($unconfirmTask, $task) {
					try {
						$success = $task->save() && UnconfirmTask::destroy($unconfirmTask->id);
						if(!$success) {
							DB::rollBack();
						} else {
							$receiverIds = $task->taskAssignees->pluck('account_id')->toArray();
							Notification::createNotification($receiverIds, NotificationTypeEnum::TaskAssigned, $task->id, trans('I014'), $task->title);
						}
						return $success;
					} catch (Exception $ex) {
						Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
						Log::error($ex);
						DB::rollBack();
						return $this->responseResult(null, false);
					}
				});
			}
		}

		return $this->responseResult(null, $success);
	}
	
	public function approveDone(Request $request) {
		$taskId = $request->input('task_id');
		$rating = $request->input('rating');

		$accountId = $this->jwtAccount->id;
		$success = false;

		$task = Task::find($taskId);
		if(!empty($task)) {
			$unconfirmTask = UnconfirmTask::where('task_id', $task->id)->first();
			if($task->creater_id == $accountId && !empty($unconfirmTask)) {
				$task->status = TaskStatusEnum::Done;
				$task->rating = $rating;
				$task->note = $unconfirmTask->reason;
				
				$success = DB::transaction(function () use ($unconfirmTask, $task) {
					try {
						$success = $task->save() && UnconfirmTask::destroy($unconfirmTask->id);
						if(!$success) {
							DB::rollBack();
						} else {
							$receiverIds = $task->taskAssignees->pluck('account_id')->toArray();
							Notification::createNotification($receiverIds, NotificationTypeEnum::TaskAssigned, $task->id, trans('I013'), $task->title);
						}
						return $success;
					} catch (Exception $ex) {
						Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
						Log::error($ex);
						DB::rollBack();
						return $this->responseResult(null, false);
					}
				});
			}
		}

		return $this->responseResult(null, $success);
	}
	
	public function rejectRequest(Request $request) {
        $taskId = $request->input('task_id');

		$accountId = $this->jwtAccount->id;
		$success = false;

		$task = Task::find($taskId);
		$currStatus = $task->status;
		if(!empty($task)) {
			$unconfirmTask = UnconfirmTask::where('task_id', $task->id)->first();
			if($task->creater_id == $accountId && !empty($unconfirmTask)) {
				$task->status = TaskStatusEnum::New;
				$task->rating = 0;
				$task->actual_end_time = null;
				
				$success = DB::transaction(function () use ($unconfirmTask, $task, $currStatus) {
					try {
						$success = $task->save() && UnconfirmTask::destroy($unconfirmTask->id);
						if(!$success) {
							DB::rollBack();
						} else {
							$title = $currStatus == TaskStatusEnum::Review ? trans('I011') : trans('I012');
							$receiverIds = $task->taskAssignees->pluck('account_id')->toArray();
							Notification::createNotification($receiverIds, NotificationTypeEnum::TaskAssigned, $task->id, $title, $task->title);
						}
						return $success;
					} catch (Exception $ex) {
						Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
						Log::error($ex);
						DB::rollBack();
						return $this->responseResult(null, false);
					}
				});
			}
		}

		return $this->responseResult(null, $success);
    }
}
