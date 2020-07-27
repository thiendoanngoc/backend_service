<?php

namespace App\Http\Controllers\Client;

use App\Enums\NotificationTypeEnum;
use App\Enums\TaskRoleEnum;
use App\Enums\TaskStatusEnum;
use App\Enums\TaskReminderTypeEnum;
use App\Enums\TaskPriorityEnum;
use App\Enums\TaskFilterTypeEnum;
use App\Enums\TaskSortTypeEnum;
use App\Http\Controllers\Controller;
use App\Models\Attachment;
use App\Models\Task;
use App\Models\TaskAssignee;
use App\Models\TaskAttachment;
use App\Models\Account;
use App\Models\Staff;
use App\Models\UnconfirmTask;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\Notification;

class TaskController extends Controller
{
	public function index()
	{
		$validated = $this->validateRequest([
			'type' => 'required',
			'task_role' => '',
			'status' => '',
			'task_creater' => '',
			'year' => '',
			'start_date' => '',
			'end_date' => '',
			'outdate_end_date' => '',
			'keywords' => '',
			'sort' => '',
			'sort' => Rule::in(TaskSortTypeEnum::$types),
		]);

		$taskRole = $validated['task_role'] ?? null;
		$status = $validated['status'] ?? null;
		$taskCreater = $validated['task_creater'] ?? null;
		$year = $validated['year'] ?? null;
		$startDate = $validated['start_date'] ?? null;
		$endDate = $validated['end_date'] ?? null;
		$outdateEndDate = $validated['outdate_end_date'] ?? null;
		$keywords = $validated['keywords'] ?? '';
		$sort = $validated['sort'] ?? null;

		if ($validated['type'] == 1 || $validated['type'] == 5 || $validated['type'] == 6) {
			$query = Task::where('creater_id', $this->jwtAccount->id);
		} else {
			if($validated['type'] == 4) {
				$query = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
					->where(function ($query) {
						$query->where('task_assignees.account_id', $this->jwtAccount->id)
							->orWhere('creater_id', $this->jwtAccount->id);
					});
			} else {
				$query = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
					->where('task_assignees.account_id', $this->jwtAccount->id);
			}

			if ($taskRole) {
				$query = $query->where('task_assignees.task_role', '=', $taskRole);
			}

			if ($taskCreater) {
				$query = $query->where('tasks.creater_id', '=', $taskCreater);
			}
		}

		if ($validated['type'] != 3 && $validated['type'] != 4 && $validated['type'] != 5) {
			if ($status) {
				$query = $query->where('tasks.status', $status);
			} else {
				$query = $query->where('tasks.status', '!=', TaskStatusEnum::Done)
					->where('tasks.status', '!=', TaskStatusEnum::Cancel);
			}
		}

		if($year){
			$query = $query->whereYear('tasks.created_at', '=', $year);
		} else {
			if ($startDate) {
				$query = $query->where('tasks.created_at', '>=', $startDate);
			}
	
			if ($endDate) {
				$query = $query->where('tasks.created_at', '<', $endDate);
			}
		}

		if ($keywords) {
			$query = $query->where(function ($query) use ($keywords) {
				$query->orWhere('tasks.title', 'like', '%' . $keywords . '%')
					->orWhere('tasks.description', 'like', '%' . $keywords . '%');
			});
		}

		// if($validated['type'] != 6) {
		// 	$query = $query->orderBy('updated_at', 'desc');
		// }

		$tasks = array();
		switch (intval($validated['type'])) {
			case 1:
				//assign tasks
				$tasks = $query->get();
				$tasks = $this->sortTaskList($sort, $tasks)->values();
				break;
			case 2:
				// assigned tasks
				$tasks = $query->get();
				$tasks = $this->sortTaskList($sort, $tasks)->values();
				break;
			case 3:
				// done tasks
				$completedAssignedTasks = $query->where('tasks.status', TaskStatusEnum::Done)->get();

				// Begin block code below is get completed task assign of creater
				$query = Task::where('creater_id', $this->jwtAccount->id)
					->where('status', TaskStatusEnum::Done);

				if ($startDate) {
					$query = $query->where('tasks.created_at', '>=', $startDate);
				}

				if ($endDate) {
					$query = $query->where('tasks.created_at', '<', $endDate);
				}

				if ($keywords) {
					$query = $query->where(function ($query) use ($keywords) {
						$query->orWhere('tasks.title', 'like', '%' . $keywords . '%')
							->orWhere('tasks.description', 'like', '%' . $keywords . '%');
					});
				}

				$completedAssignTasks = $query->get();
				// End this block

				foreach ($completedAssignTasks as $completedAssignTask) {
					$completedAssignTask->creater;
					$completedAssignTask->hoster = $completedAssignTask->getAccountByTaskRole(TaskRoleEnum::Hoster);
					$completedAssignTask->supporter = $completedAssignTask->getAccountByTaskRole(TaskRoleEnum::Supporter);
				}

				foreach ($completedAssignedTasks as $completedAssignedTask) {
					$completedAssignedTask->creater;
					$completedAssignedTask->hoster = $completedAssignedTask->getAccountByTaskRole(TaskRoleEnum::Hoster);
					$completedAssignedTask->supporter = $completedAssignedTask->getAccountByTaskRole(TaskRoleEnum::Supporter);
				}
				$tasks = $completedAssignTasks->concat($completedAssignedTasks);
				$tasks = $this->sortTaskList($sort, $tasks)->values();
				break;

			case 4:
				// outdate tasks
				$now = date('Y-m-d H:i:s');
				$query = $query->where('tasks.status', '!=', TaskStatusEnum::Cancel);
				$query = $query->where('tasks.end_time', '<', $now)
					->where(function ($query) {
						$query->where('end_time', '<', DB::raw('actual_end_time'))
							->orWhereNull('actual_end_time');
					});
				$tasks = $query->get();
				$tasks = $this->sortTaskList($sort, $tasks)->values();
				break;
			case 5:
				// outdate assign tasks
				$now = $outdateEndDate ? $outdateEndDate : date('Y-m-d H:i:s');
				$query = $query->where('tasks.status', '!=', TaskStatusEnum::Cancel);
				$query = $query->where('tasks.end_time', '<', $now)
					->where(function ($query) {
						$query->where('end_time', '<', DB::raw('actual_end_time'))
							->orWhereNull('actual_end_time');
					});
				$tasks = $query->get();
				$tasks = $this->sortTaskList($sort, $tasks)->values();
				break;
			case 6:
				// priority assign tasks
				$query = $query->orderBy('task_priority', 'desc');
				$tasks = $query->get();
				break;

			default:
				break;
		}

		$now = date('Y-m-d H:i:s');

		if ($validated['type'] != 3) {
			foreach ($tasks as $task) {
				$task->creater;
				$task->hoster = $task->getAccountByTaskRole(TaskRoleEnum::Hoster);
				$task->supporter = $task->getAccountByTaskRole(TaskRoleEnum::Supporter);
				$task->taskGroup;
				$avatarPaths = array();
				array_push($avatarPaths, $task->creater->image);
				array_push($avatarPaths, $task->hoster->image);
				foreach ($task->supporter as $supporter) {
					array_push($avatarPaths, $supporter->image);
				}
				$task['avatar_paths'] = $avatarPaths;
				$isOutdate = false;
				if($task->status != TaskStatusEnum::Cancel && strtotime($task->end_time) < strtotime($now)) {
					if(strtotime($task->end_time) < strtotime($task->actual_end_time) || empty($task->actual_end_time)) {
						$isOutdate = true;
					}
				}
				$task['is_outdate'] = $isOutdate;
			}
		} else {
			foreach ($tasks as $task) {
				$task->taskGroup;
				$avatarPaths = array();
				array_push($avatarPaths, $task->creater->image);
				array_push($avatarPaths, $task->hoster->image);
				foreach ($task->supporter as $supporter) {
					array_push($avatarPaths, $supporter->image);
				}
				$task['avatar_paths'] = $avatarPaths;
				if($task->status != TaskStatusEnum::Cancel && strtotime($task->end_time) < strtotime($now)) {
					if(strtotime($task->end_time) < strtotime($task->actual_end_time) || empty($task->actual_end_time)) {
						$isOutdate = true;
					}
				}
				$task['is_outdate'] = $isOutdate;
			}
		}

		return $this->responseResult($tasks);
	}

	private function sortTaskList($type, $tasks)
	{
		if(!$type) {
			return $tasks;
		}
		switch($type){
			case TaskSortTypeEnum::Newest:
				$tasks = $tasks->sortByDesc('updated_at');
				break;
		
			case TaskSortTypeEnum::Oldest:
				$tasks = $tasks->sortBy('updated_at');
				break;
		
			case TaskSortTypeEnum::DeadlineAsc:
				$tasks = $tasks->sortBy('end_time');
				break;
		
			case TaskSortTypeEnum::DeadlineDesc:
				$tasks = $tasks->sortByDesc('end_time');
				break;
		}
		return $tasks;
	}

	public function show(Task $task)
	{
		$task->attachment_paths = $task->getAttachmentPaths();
		$task->creater;
		$task->hoster = $task->getAccountByTaskRole(TaskRoleEnum::Hoster);
		$task->supporter = $task->getAccountByTaskRole(TaskRoleEnum::Supporter);
		return $this->responseResult($task);
	}

	public function store()
	{
		$validated = $this->validateRequest([
			'title' => 'required|max:128',
			'description' => 'required',
			'task_group_id' => 'required',
			'hoster_id' => 'required',
			'supporter_id' => 'array',
			'end_time' => 'required|date',
			'reminder_before_time' => 'required|date|before:end_time',
			'reminder_type' => 'required',
			'reminder_type' => Rule::in(TaskReminderTypeEnum::$types),
			'attachment_paths' => 'array',
			// 'task_level' => '',
			'note' => '',
			'task_priority' => 'required',
			'task_priority' => Rule::in(TaskPriorityEnum::$types),
			'start_time' => 'date|before:end_time'
		]);

		$task = new Task();

		try {
			$createrId = $this->jwtAccount->id;
			$result = DB::transaction(function () use ($task, $validated, $createrId) {
				$isError = false;

				$task->title = $validated['title'];
				$task->description = $validated['description'];
				$task->task_group_id = $validated['task_group_id'];
				$task->end_time = $validated['end_time'];
				$task->reminder_before_time = $validated['reminder_before_time'];
				$task->reminder_type = $validated['reminder_type'];
				// $task->task_level = $validated['task_level'];
				$task->note = $validated['note'];
				$task->task_priority = $validated['task_priority'];
				if(request()->input('start_time')) {
					$task->start_time = $validated['start_time'];
				}
				$task->creater_id = $createrId;
				$isError |= !$task->save();

				// case 1: is sublevel and same department
				// case 2: is sublevel and creater is at level 1
				$check = $this->isSubLevel($createrId, $validated['hoster_id'])
					&& ($this->isAtLevel($createrId, 1) || $this->isSameDepartment($createrId, $validated['hoster_id']));
				if (!$check) {
					Log::error('Invalid sublevel hoster');
					DB::rollBack();
					return false;
				}

				$taskAssignee = new TaskAssignee();
				$taskAssignee->task_id = $task->id;
				$taskAssignee->account_id = $validated['hoster_id'];
				$taskAssignee->task_role = TaskRoleEnum::Hoster;
				$isError |= !$taskAssignee->save();

				// $supporterId = $validated['supporter_id'] ?? null;
				foreach ($validated['supporter_id'] as $supporterId) {
					$check = $this->isSubLevel($createrId, $supporterId)
						&& ($this->isAtLevel($createrId, 1) || $this->isSameDepartment($createrId, $supporterId));
					if (!$check) {
						Log::error('Invalid sublevel supporter');
						DB::rollBack();
						return false;
					}
					$taskAssignee = new TaskAssignee();
					$taskAssignee->task_id = $task->id;
					$taskAssignee->account_id = $supporterId;
					$taskAssignee->task_role = TaskRoleEnum::Supporter;
					$isError |= !$taskAssignee->save();
				}

				foreach ($validated['attachment_paths'] as $attachment_path) {
					$attachment = new Attachment();
					$attachment->path = $attachment_path;
					$isError |= !$attachment->save();

					$taskAttachment = new TaskAttachment();
					$taskAttachment->task_id = $task->id;
					$taskAttachment->attachment_id = $attachment->id;
					$isError |= !$taskAttachment->save();
				}

				return !$isError;
			});

			if ($result) {
				// task_assigned = 2, task_assign = 1
				$data = array('task_id' => $task->id, 'task_type' => 2);
				$task->notifyAllStakeHolders($this->connection, trans('I002'), $this->jwtAccount->id, trans('I006'), $data);

				$receiverIds = $validated['supporter_id'];
				array_push($receiverIds, $validated['hoster_id']);
				Notification::createNotification($receiverIds, NotificationTypeEnum::TaskAssigned, $task->id, trans('I002'), $task->title);

				return $this->responseResult();
			} else {
				return $this->responseResult(null, false);
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
	}

	private function isSubLevel($parentId, $childId)
	{
		$parent = Staff::join('positions', 'staffs.position_id', '=', 'positions.id')
			->where('staffs.account_id', $parentId)
			->get();
		$parentLevel = $parent[0]['level'];
		$childLevel = $parentLevel + 2;

		$countChild = Staff::join('positions', 'staffs.position_id', '=', 'positions.id')
			->where('staffs.account_id', $childId)
			->where('positions.level', '>', $parentLevel)
			->where('positions.level', '<=', $childLevel)
			->count();

		return $countChild != 0;
	}

	private function isSameDepartment($id1, $id2)
	{
		$department1 = Staff::join('positions', 'staffs.position_id', '=', 'positions.id')
			->where('staffs.account_id', $id1)
			->pluck('positions.department_id')
			->first();

		$department2 = Staff::join('positions', 'staffs.position_id', '=', 'positions.id')
			->where('staffs.account_id', $id2)
			->pluck('positions.department_id')
			->first();

		return $department1 == $department2;
	}

	private function isAtLevel($id, $level)
	{
		$targetLevel = Staff::join('positions', 'staffs.position_id', '=', 'positions.id')
			->where('staffs.account_id', $id)
			->pluck('positions.level')
			->first();

		return $targetLevel == $level;
	}

	private function isSameLevel($id1, $id2)
	{
		$level1 = Staff::join('positions', 'staffs.position_id', '=', 'positions.id')
			->where('staffs.account_id', $id1)
			->pluck('positions.level')
			->first();

		$level2 = Staff::join('positions', 'staffs.position_id', '=', 'positions.id')
			->where('staffs.account_id', $id2)
			->pluck('positions.level')
			->first();

		return $level1 == $level2;
	}

	public function update(Task $task)
	{
		$validated = $this->validateRequest([
			'title' => 'required|max:128',
			'description' => 'required',
			'task_group_id' => 'required',
			'hoster_id' => 'required',
			'supporter_id' => 'array',
			'end_time' => 'required|date',
			'reminder_before_time' => 'required|date|before:end_time',
			'reminder_type' => 'required',
			'reminder_type' => Rule::in(TaskReminderTypeEnum::$types),
			'attachment_paths' => 'array',
			// 'task_level' => '',
			'status' => Rule::in(TaskStatusEnum::$types),
			'note' => '',
			'task_priority' => 'required',
			'task_priority' => Rule::in(TaskPriorityEnum::$types),
			'start_time' => 'date|before:end_time'
		]);

		try {
			$updaterId = $this->jwtAccount->id;

			if ($this->isSameLevel($task->creater_id, $updaterId) && $this->isSameDepartment($task->creater_id, $updaterId)) {
				$result = DB::transaction(function () use ($task, $validated, $updaterId) {
					$isError = false;

					$task->title = $validated['title'];
					$task->description = $validated['description'];
					$task->task_group_id = $validated['task_group_id'];
					$task->end_time = $validated['end_time'];
					$task->reminder_before_time = $validated['reminder_before_time'];
					$task->reminder_type = $validated['reminder_type'];
					// $task->task_level = $validated['task_level'];
					$task->note = $validated['note'] ?? null;
					$task->task_priority = $validated['task_priority'];
					$task->updater_id = $updaterId;
					if($task->status != TaskStatusEnum::Review && $task->status != TaskStatusEnum::Done) {
						if($validated['status'] == TaskStatusEnum::Review || $validated['status'] == TaskStatusEnum::Done) {
							$task->actual_end_time = date('Y-m-d H:i:s');
						}
					}
					if(request()->input('start_time')) {
						$task->start_time = $validated['start_time'];
					}
					if(request()->input('status')) {
						$task->status = $validated['status'];
					}
					$isError |= !$task->save();

					$isError |= !DB::statement('delete from task_assignees where task_id = ' . $task->id);

					// case 1: is sublevel and same department
					// case 2: is sublevel and creater is at level 1
					$check = $this->isSubLevel($updaterId, $validated['hoster_id'])
						&& ($this->isAtLevel($updaterId, 1) || $this->isSameDepartment($updaterId, $validated['hoster_id']));
					if (!$check) {
						Log::error('Invalid sublevel hoster');
						DB::rollBack();
						return false;
					}

					$taskAssignee = new TaskAssignee();
					$taskAssignee->task_id = $task->id;
					$taskAssignee->account_id = $validated['hoster_id'];
					$taskAssignee->task_role = TaskRoleEnum::Hoster;
					$isError |= !$taskAssignee->save();

					foreach ($validated['supporter_id'] as $supporterId) {
						$check = $this->isSubLevel($updaterId, $supporterId)
							&& ($this->isAtLevel($updaterId, 1) || $this->isSameDepartment($updaterId, $supporterId));
						if (!$check) {
							Log::error('Invalid sublevel supporter');
							DB::rollBack();
							return false;
						}
						$taskAssignee = new TaskAssignee();
						$taskAssignee->task_id = $task->id;
						$taskAssignee->account_id = $supporterId;
						$taskAssignee->task_role = TaskRoleEnum::Supporter;
						$isError |= !$taskAssignee->save();
					}

					// add new attachments record or not if already exist
					$oldAttachmentPaths = $task->getAttachmentPaths();
					foreach ($validated['attachment_paths'] as $attachment_path) {
						$oldAttachmentExists = false;
						foreach ($oldAttachmentPaths as $oldAttachmentPath) {
							if ($oldAttachmentPath === $attachment_path) {
								$oldAttachmentExists = true;
								break;
							}
						}

						if (!$oldAttachmentExists) {
							$attachment = new Attachment();
							$attachment->path = $attachment_path;
							$isError |= !$attachment->save();

							$taskAttachment = new TaskAttachment();
							$taskAttachment->task_id = $task->id;
							$taskAttachment->attachment_id = $attachment->id;
							$isError |= !$taskAttachment->save();
						}
					}

					// remove old attachments not exists
					foreach ($oldAttachmentPaths as $oldAttachmentPath) {
						$oldAttachmentExists = false;
						foreach ($validated['attachment_paths'] as $attachment_path) {
							if ($oldAttachmentPath === $attachment_path) {
								$oldAttachmentExists = true;
								break;
							}
						}

						if (!$oldAttachmentExists) {
							$unusedAttachment = Attachment::where('path', $oldAttachmentPath)->first();
							$isError |= !DB::statement('delete from task_attachments where attachment_id = ' . $unusedAttachment->id);
							$isError |= !$unusedAttachment->delete();
						}
					}

					return !$isError;
				});
				if ($result) {
					// task_assigned = 2, task_assign = 1
					$data = array('task_id' => $task->id, 'task_type' => 2);
					$task->notifyAllStakeHolders($this->connection, trans('I003'), $this->jwtAccount->id, trans('I007'), $data);

					$receiverIds = $validated['supporter_id'];
					array_push($receiverIds, $validated['hoster_id']);
					Notification::createNotification($receiverIds, NotificationTypeEnum::TaskAssigned, $task->id, trans('I003'), $task->title);

					return $this->responseResult();
				} else {
					return $this->responseResult(null, false);
				}
			} else {
				Log::error('Logged user does not have right to update');
				DB::rollBack();
				return $this->responseResult(null, false);
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
	}

	public function updateAssignedTask(Task $task)
	{
		$validated = $this->validateRequest([
			'status' => 'required',
			'status' => Rule::in(TaskStatusEnum::$types),
			'reason' => '',
		]);

		try {
			$updaterId = $this->jwtAccount->id;

			$hosterId = TaskAssignee::where('task_id', $task->id)
				->where('task_role', TaskRoleEnum::Hoster)
				->pluck('account_id')
				->first();

			$result = false;
			if (!empty($hosterId) && $updaterId == $hosterId) {
				switch ($validated['status']) {
					case TaskStatusEnum::Reject:
						if(!empty($validated['reason'])) {
							$unconfirm = UnconfirmTask::where('task_id', $task->id)->first();
							if(empty($unconfirm)) {
								$unconfirm = new UnconfirmTask();
							}
							$result = DB::transaction(function () use ($task, $unconfirm, $hosterId, $validated) {
								$unconfirm->task_id = $task->id;
								$unconfirm->update_assignee_id = $hosterId;
								$unconfirm->reason = $validated['reason'];
								$unconfirm->update_due_date = $task->end_time;
								$unconfirm->status = $validated['status'];
								$unconfirm->creater_id = $hosterId;

								$task->status = $validated['status'];
								$task->updater_id = $hosterId;
								
								if($unconfirm->save() && $task->save()) {
									return true;
								} else {
									Log::error('Update status failed');
									DB::rollBack();
									return false;
								}
							});
						}
						break;
					case TaskStatusEnum::Review:
						$unconfirm = UnconfirmTask::where('task_id', $task->id)->first();
						if(empty($unconfirm)) {
							$unconfirm = new UnconfirmTask();
						}
						$result = DB::transaction(function () use ($task, $unconfirm, $hosterId, $validated) {
							$unconfirm->task_id = $task->id;
							$unconfirm->update_assignee_id = $hosterId;
							$unconfirm->reason = $validated['reason'] ? $validated['reason'] : 'review';
							$unconfirm->update_due_date = $task->end_time;
							$unconfirm->status = $validated['status'];
							$unconfirm->creater_id = $hosterId;
							
							$task->status = $validated['status'];
							$task->actual_end_time = date('Y-m-d H:i:s');
							$task->updater_id = $hosterId;
							
							if($unconfirm->save() && $task->save()) {
								return true;
							} else {
								Log::error('Update status failed');
								DB::rollBack();
								return false;
							}
						});
						break;
					default:
						$result = DB::transaction(function () use ($task, $validated, $updaterId) {
							$isError = false;
							
							$task->status = $validated['status'];
							$task->updater_id = $updaterId;
							$isError = !$task->save();
		
							return !$isError;
						});
						break;
				}
			}

			if ($result) {
				if($validated['status'] == TaskStatusEnum::Review || $validated['status'] == TaskStatusEnum::Reject) {
					$data = array('task_id' => $task->id, 'task_type' => 3);
					$message = $validated['status'] == TaskStatusEnum::Review ? trans('I008') : trans('I009');
					$title = $validated['status'] == TaskStatusEnum::Review ? trans('I008') : trans('I009');
					$task->notifyAllStakeHolders($this->connection, $message, $this->jwtAccount->id, $title, $data);

					$receiverIds = array($task->creater_id);
					Notification::createNotification($receiverIds, NotificationTypeEnum::TaskAssign, $task->id, $title, $task->title);

					$receiverIds = $task->taskAssignees->where('account_id', '!=', $this->jwtAccount->id)->pluck('account_id')->toArray();
					Notification::createNotification($receiverIds, NotificationTypeEnum::TaskAssigned, $task->id, trans('I010'), $task->title);
				} else {
					$typeAssign = [TaskStatusEnum::InProgress, TaskStatusEnum::Done, TaskStatusEnum::Reject];
					// task_assigned = 2, task_assign = 1
					$type = in_array($task->status, $typeAssign) ? 1 : 2;
					$data = array('task_id' => $task->id, 'task_type' => $type);
					$task->notifyAllStakeHolders($this->connection, trans('I003'), $this->jwtAccount->id, trans('I010'), $data);

					$receiverIds = $task->taskAssignees->where('account_id', '!=', $this->jwtAccount->id)->pluck('account_id')->toArray();
					Notification::createNotification($receiverIds, NotificationTypeEnum::TaskAssigned, $task->id, trans('I010'), $task->title);
				}

				return $this->responseResult();
			} else {
				Log::error('Failed to update task');
				return $this->responseResult(null, false);
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
	}

	public function taskStatistics()
	{
		$validated = $this->validateRequest([
			'start_date' => '',
			'end_date' => ''
		]);

		$taskStatistics = new \stdClass();

		if(request()->input('start_date') && request()->input('end_date')) {
			$validated = $this->validateRequest([
				'start_date' => 'date',
				'end_date' => 'date|after:start_date'
			]);
			$query = Task::where('created_at', '>=', $validated['start_date'])
				->where('created_at', '<', $validated['end_date']);
		} else {
			$query = Task::query();
		}
		
		$now = date('Y-m-d H:i:s');

		$completedAssignedQuery = clone $query;
		$completedAssignQuery = clone $query;
		$assignedQuery = clone $query;
		$assignQuery = clone $query;
		$outdateQuery = clone $query;

		$completedAssignedTasks = $completedAssignedQuery->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
			->where('task_assignees.account_id', $this->jwtAccount->id)
			->where('status', TaskStatusEnum::Done)->count();

		$completedAssignTasks = $completedAssignQuery->where('creater_id', $this->jwtAccount->id)
			->where('status', TaskStatusEnum::Done)->count();

		$assignedTasks = $assignedQuery->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
			->where('task_assignees.account_id', $this->jwtAccount->id)
			->where('status', '!=', TaskStatusEnum::Done)
			->where('status', '!=', TaskStatusEnum::Cancel)->count();

		$assignTasks = $assignQuery->where('creater_id', $this->jwtAccount->id)
			->where('tasks.status', '!=', TaskStatusEnum::Done)
			->where('tasks.status', '!=', TaskStatusEnum::Cancel)
			->count();

		$outdateTasks = $outdateQuery->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
			->where(function ($query) {
				$query->where('task_assignees.account_id', $this->jwtAccount->id)
					->orWhere('creater_id', $this->jwtAccount->id);
			})
			->where('tasks.status', '!=', TaskStatusEnum::Cancel)
			->where('tasks.end_time', '<', $now)
			->where(function ($query) {
				$query->where('end_time', '<', DB::raw('actual_end_time'))
					->orWhereNull('actual_end_time');
			})
			->count();

		$taskStatistics->completed_tasks = $completedAssignTasks + $completedAssignedTasks;
		$taskStatistics->assigned_tasks = $assignedTasks;
		$taskStatistics->assign_tasks = $assignTasks;
		$taskStatistics->outdate_tasks = $outdateTasks;

		return $this->responseResult($taskStatistics);
	}

	public function taskReport()
	{
		$accountId = $this->jwtAccount->id;
		$now = date('Y-m-d H:i:s');

		// total tasks
		$total = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_role', '=', TaskRoleEnum::Hoster)
			->where('account_id', $accountId)
			->where('status', '=', TaskStatusEnum::InProgress)
			->orWhere('status', '=', TaskStatusEnum::Done)
			->count();

		// unfinished tasks
		$unfinished = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_role', '=', TaskRoleEnum::Hoster)
			->where('account_id', $accountId)
			->where('status', '=', TaskStatusEnum::InProgress)
			->where('end_time', '>=', $now)
			->count();

		// overdate unfinished tasks
		$overdateUnfinished = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_role', '=', TaskRoleEnum::Hoster)
			->where('account_id', $accountId)
			->where('status', '=', TaskStatusEnum::InProgress)
			->where('end_time', '<', $now)
			->count();

		// finished intime tasks
		$intimeFinished = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_role', '=', TaskRoleEnum::Hoster)
			->where('account_id', $accountId)
			->where('status', '=', TaskStatusEnum::Done)
			->where('end_time', '>=', DB::raw('actual_end_time'))
			->count();

		// finished overdate tasks
		$overdateFinished = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_role', '=', TaskRoleEnum::Hoster)
			->where('account_id', $accountId)
			->where('status', '=', TaskStatusEnum::Done)
			->where('end_time', '<', DB::raw('actual_end_time'))
			->count();

		$tasks = new \stdClass();

		$tasks->total = $total;
		$tasks->unfinished = $unfinished;
		$tasks->overdate_unfinished = $overdateUnfinished;
		$tasks->intime_finished = $intimeFinished;
		$tasks->overdate_finished = $overdateFinished;

		return $this->responseResult($tasks);
	}

	public function taskMakerList()
	{
		$accountId = $this->jwtAccount->id;

		// query all account that assigned task to this account id
		$makerList = Account::whereIn('id', function ($query) use ($accountId) {
			$query->from('task_assignees')
				->join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
				->where('account_id', $accountId)
				->distinct()
				->select('creater_id');
		})
			->select('id', 'full_name')
			->get();

		$taskMakerList = array();

		return $this->responseResult($makerList);
	}

	public function taskReportFilter()
	{
		$validated = $this->validateRequest([
			'start_date' => 'date',
			'end_date' => 'date|after:start_date',
			'start_month' => '',
			'end_month' => '',
			'start_quarter' => '',
			'end_quarter' => '',
			'year' => '',
			'assign_ids' => 'array',
			'assigned_ids' => 'array',
			'group_ids' => 'array',
			'status' => 'required',
		]);

		$now = date('Y-m-d H:i:s');

		$startDate = $validated['start_date'] ?? null;
		$endDate = $validated['end_date'] ?? null;
		$startMonth = $validated['start_month'] ?? null;
		$endMonth = $validated['end_month'] ?? null;
		$startQuarter = $validated['start_quarter'] ?? null;
		$endQuarter = $validated['end_quarter'] ?? null;
		$year = $validated['year'] ?? null;
		$assignIds = $validated['assign_ids'] ?? null;
		$assignedIds = $validated['assigned_ids'] ?? null;
		$groupIds = $validated['group_ids'] ?? null;
		$status = $validated['status'] ?? null;

		if ($startDate && $endDate) {
			$endDateTime = date('Y-m-d 23:59:59', strtotime($endDate));
			$query = Task::where('tasks.created_at', '>=', $startDate)
				->where('tasks.created_at', '<=', $endDateTime);
		}
		else if($startMonth && $endMonth && $year) {
			$query = Task::whereYear('tasks.created_at', '=', $year)
				->whereMonth('tasks.created_at', '>=', $startMonth)
				->whereMonth('tasks.created_at', '<=', $endMonth);
		}
		else if($startQuarter && $endQuarter && $year) {
			$query = Task::whereYear('tasks.created_at', '=', $year)
				->where(DB::raw('QUARTER(tasks.created_at)'), '>=', $startQuarter)
				->where(DB::raw('QUARTER(tasks.created_at)'), '<=', $endQuarter);
		}
		else if($year) {
			$query = Task::whereYear('tasks.created_at', '=', $year);
		}
		else {
			return $this->responseResult(null, false);
		}

		if($groupIds) {
			$query = $query->whereIn('tasks.task_group_id', $groupIds);
		}

		if($status) {
			if($status == 0) {
				$query = $query->whereIn('tasks.status', TaskStatusEnum::$types);
			} else if($status == 7) {	// overdate tasks
				$query = $query->where('tasks.status', '!=', TaskStatusEnum::Cancel)
				->where('tasks.status', '!=', TaskStatusEnum::Done)
				->where('tasks.end_time', '<', $now)
				->where(function ($query) {
					$query->where('end_time', '<', DB::raw('actual_end_time'))
						->orWhereNull('actual_end_time');
				});
			} else {
				$query = $query->where('tasks.status', $status);
			}
		}

		if($assignIds && $assignedIds) {
			$query = $query->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
			->where('task_assignees.task_role', TaskRoleEnum::Hoster)
			->where(function($q) use($assignIds, $assignedIds) {
				$q->whereIn('task_assignees.account_id', $assignedIds)
				->orWhereIn('tasks.creater_id', $assignIds);
			});
		}
		else if($assignIds) {
			$query = $query->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
				->where('task_assignees.account_id', $this->jwtAccount->id)
				->where('task_assignees.task_role', TaskRoleEnum::Hoster)
				->whereIn('tasks.creater_id', $assignIds);
		}
		else if($assignedIds) {
			$query = $query->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
				->where('task_assignees.task_role', TaskRoleEnum::Hoster)
				->where('tasks.creater_id', $this->jwtAccount->id)
				->whereIn('task_assignees.account_id', $assignedIds);
		}
		else {
			$query = $query->join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
				->where('task_assignees.task_role', TaskRoleEnum::Hoster)
				->where(function($q) {
					$q->where('task_assignees.account_id', $this->jwtAccount->id)
					->orWhere('tasks.creater_id', $this->jwtAccount->id);
				});
		}

		$overdateQuery = clone $query;
		$doneQuery = clone $query;
		$assignQuery = clone $query;
		$assignedQuery = clone $query;

		$overdateQuery = $overdateQuery->where('tasks.status', '!=', TaskStatusEnum::Cancel)
			->where('tasks.end_time', '<', $now)
			->where(function ($query) {
				$query->where('end_time', '<', DB::raw('actual_end_time'))
					->orWhereNull('actual_end_time');
			});
		$overdateCount = $overdateQuery->count();
		$overdateList = $overdateQuery->get();

		$doneQuery = $doneQuery->where('tasks.status', '=', TaskStatusEnum::Done);
		$doneCount = $doneQuery->count();
		$doneList = $doneQuery->get();

		$assignCount = 0;
		$assignedCount = 0;
		$assignList = [];
		$assignedList = [];

		if(($assignIds && $assignedIds) || (!$assignIds && !$assignedIds)) {
			$assignedCount = $assignedQuery->where('task_assignees.account_id', $this->jwtAccount->id)
				->count();
			$assignedList = $assignedQuery->where('task_assignees.account_id', $this->jwtAccount->id)
				->get();

			$assignCount = $assignQuery->where('tasks.creater_id', $this->jwtAccount->id)
				->count();
			$assignList = $assignQuery->where('tasks.creater_id', $this->jwtAccount->id)
				->get();
		}
		else if($assignIds) {
			$assignedCount = $assignedQuery->count();
			$assignedList = $assignedQuery->get();
		}
		else if($assignedIds) {
			$assignCount = $assignQuery->count();
			$assignList = $assignQuery->get();
		}
		
		$filteredTasks = $query->get();

		foreach ($filteredTasks as $task) {
			$isOutdate = false;
			if($task->status != TaskStatusEnum::Cancel && strtotime($task->end_time) < strtotime($now)) {
				if(strtotime($task->end_time) < strtotime($task->actual_end_time) || empty($task->actual_end_time)) {
					$isOutdate = true;
				}
			}
			$task['is_outdate'] = $isOutdate;
			$task->creater;
			$task->hoster = $task->getAccountByTaskRole(TaskRoleEnum::Hoster);
			$task->supporter = $task->getAccountByTaskRole(TaskRoleEnum::Supporter);
			$task->taskGroup;
			$avatarPaths = array();
			array_push($avatarPaths, $task->creater->image);
			array_push($avatarPaths, $task->hoster->image);
			foreach ($task->supporter as $supporter) {
				array_push($avatarPaths, $supporter->image);
			}
			$task['avatar_paths'] = $avatarPaths;
		}

		foreach ($assignedList as $task) {
			$isOutdate = false;
			if($task->status != TaskStatusEnum::Cancel && strtotime($task->end_time) < strtotime($now)) {
				if(strtotime($task->end_time) < strtotime($task->actual_end_time) || empty($task->actual_end_time)) {
					$isOutdate = true;
				}
			}
			$task['is_outdate'] = $isOutdate;
			$task->creater;
			$task->hoster = $task->getAccountByTaskRole(TaskRoleEnum::Hoster);
			$task->supporter = $task->getAccountByTaskRole(TaskRoleEnum::Supporter);
			$task->taskGroup;
			$avatarPaths = array();
			array_push($avatarPaths, $task->creater->image);
			array_push($avatarPaths, $task->hoster->image);
			foreach ($task->supporter as $supporter) {
				array_push($avatarPaths, $supporter->image);
			}
			$task['avatar_paths'] = $avatarPaths;
		}

		foreach ($assignList as $task) {
			$isOutdate = false;
			if($task->status != TaskStatusEnum::Cancel && strtotime($task->end_time) < strtotime($now)) {
				if(strtotime($task->end_time) < strtotime($task->actual_end_time) || empty($task->actual_end_time)) {
					$isOutdate = true;
				}
			}
			$task['is_outdate'] = $isOutdate;
			$task->creater;
			$task->hoster = $task->getAccountByTaskRole(TaskRoleEnum::Hoster);
			$task->supporter = $task->getAccountByTaskRole(TaskRoleEnum::Supporter);
			$task->taskGroup;
			$avatarPaths = array();
			array_push($avatarPaths, $task->creater->image);
			array_push($avatarPaths, $task->hoster->image);
			foreach ($task->supporter as $supporter) {
				array_push($avatarPaths, $supporter->image);
			}
			$task['avatar_paths'] = $avatarPaths;
		}

		foreach ($overdateList as $task) {
			$isOutdate = false;
			if($task->status != TaskStatusEnum::Cancel && strtotime($task->end_time) < strtotime($now)) {
				if(strtotime($task->end_time) < strtotime($task->actual_end_time) || empty($task->actual_end_time)) {
					$isOutdate = true;
				}
			}
			$task['is_outdate'] = $isOutdate;
			$task->creater;
			$task->hoster = $task->getAccountByTaskRole(TaskRoleEnum::Hoster);
			$task->supporter = $task->getAccountByTaskRole(TaskRoleEnum::Supporter);
			$task->taskGroup;
			$avatarPaths = array();
			array_push($avatarPaths, $task->creater->image);
			array_push($avatarPaths, $task->hoster->image);
			foreach ($task->supporter as $supporter) {
				array_push($avatarPaths, $supporter->image);
			}
			$task['avatar_paths'] = $avatarPaths;
		}

		foreach ($doneList as $task) {
			$isOutdate = false;
			if($task->status != TaskStatusEnum::Cancel && strtotime($task->end_time) < strtotime($now)) {
				if(strtotime($task->end_time) < strtotime($task->actual_end_time) || empty($task->actual_end_time)) {
					$isOutdate = true;
				}
			}
			$task['is_outdate'] = $isOutdate;
			$task->creater;
			$task->hoster = $task->getAccountByTaskRole(TaskRoleEnum::Hoster);
			$task->supporter = $task->getAccountByTaskRole(TaskRoleEnum::Supporter);
			$task->taskGroup;
			$avatarPaths = array();
			array_push($avatarPaths, $task->creater->image);
			array_push($avatarPaths, $task->hoster->image);
			foreach ($task->supporter as $supporter) {
				array_push($avatarPaths, $supporter->image);
			}
			$task['avatar_paths'] = $avatarPaths;
		}

		$tasks = new \stdClass();

		$tasks->filtered_tasks = $filteredTasks;
		$tasks->assigned_tasks = $assignedList;
		$tasks->assign_tasks = $assignList;
		$tasks->overdate_tasks = $overdateList;
		$tasks->done_tasks = $doneList;
		$tasks->overdate = $overdateCount;
		$tasks->done = $doneCount;
		$tasks->assign = $assignCount;
		$tasks->assigned = $assignedCount;

		return $this->responseResult($tasks);
	}

	public function taskRating(Request $request)
	{
		$rating = $request->input('rating');
		$taskId = $request->input('task_id');

		$accountId = $this->jwtAccount->id;
		$success = false;
		$result = new \stdClass();

		$taskAssignee = TaskAssignee::firstWhere('task_id', $taskId);
		if (!$taskAssignee || $taskAssignee->account_id != $accountId) {
			$success = false;
		} else {
			$task = Task::find($taskId);
			$task->rating = $rating;
			try {
				$task->save();
				$success = true;
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

	public function declineTask(Request $request)
	{
		$taskId = $request->input('task_id');

		$accountId = $this->jwtAccount->id;
		$success = false;
		$result = new \stdClass();

		$task = Task::find($taskId);
		if ($task->creater_id == $accountId) {
			$task->status = TaskStatusEnum::Reject;
			try {
				$task->save();
				$success = true;
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

	public function getWeeklyChart()
	{
		$validated = $this->validateRequest([
			'selected_date' => 'required|date'
		]);
		$chooseDate = $validated['selected_date'];
		$accId = $this->jwtAccount->id;
		$now = date('Y-m-d H:i:s');

		$monday1 = date('Y-m-01', strtotime($chooseDate));
		$monday2 = date('Y-m-d', strtotime($monday1 . '+ 7 days'));
		$monday3 = date('Y-m-d', strtotime($monday2 . '+ 7 days'));
		$monday4 = date('Y-m-d', strtotime($monday3 . '+ 7 days'));
		$firstDayNextMonth = date('Y-m-d', strtotime(date('Y-m-t', strtotime($chooseDate)) . '+ 1 day'));

		$result = new \stdClass();

		$week1 = new \stdClass();
		$week2 = new \stdClass();
		$week3 = new \stdClass();
		$week4 = new \stdClass();

		// week 1
		$completedAssignedTasks = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_assignees.account_id', $accId)
			->where('status', TaskStatusEnum::Done)
			->where('created_at', '>=', $monday1)
			->where('created_at', '<', $monday2)->count();

		$completedAssignTasks = Task::where('creater_id', $accId)
			->where('status', TaskStatusEnum::Done)
			->where('created_at', '>=', $monday1)
			->where('created_at', '<', $monday2)->count();

		$week1->completedTasks = $completedAssignedTasks + $completedAssignTasks;
		$week1->assignedTasks = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_assignees.account_id', $accId)
			->where('status', '!=', TaskStatusEnum::Done)
			->where('status', '!=', TaskStatusEnum::Cancel)
			->where('created_at', '>=', $monday1)
			->where('created_at', '<', $monday2)
			->count();
		$week1->assignTasks = Task::where('creater_id', $accId)
			->where('status', '!=', TaskStatusEnum::Done)
			->where('status', '!=', TaskStatusEnum::Cancel)
			->where('created_at', '>=', $monday1)
			->where('created_at', '<', $monday2)
			->count();
		$week1->outdateTasks = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
			->where(function ($query) {
				$query->where('task_assignees.account_id', $this->jwtAccount->id)
					->orWhere('creater_id', $this->jwtAccount->id);
			})
			->where('tasks.status', '!=', TaskStatusEnum::Cancel)
			->where('tasks.end_time', '<', $now)
			->where(function ($query) {
				$query->where('end_time', '<', DB::raw('actual_end_time'))
					->orWhereNull('actual_end_time');
			})
			->where('created_at', '>=', $monday1)
			->where('created_at', '<', $monday2)
			->count();

		// week 2
		$completedAssignedTasks = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_assignees.account_id', $accId)
			->where('status', TaskStatusEnum::Done)
			->where('created_at', '>=', $monday2)
			->where('created_at', '<', $monday3)->count();

		$completedAssignTasks = Task::where('creater_id', $accId)
			->where('status', TaskStatusEnum::Done)
			->where('created_at', '>=', $monday2)
			->where('created_at', '<', $monday3)->count();

		$week2->completedTasks = $completedAssignedTasks + $completedAssignTasks;
		$week2->assignedTasks = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_assignees.account_id', $accId)
			->where('status', '!=', TaskStatusEnum::Done)
			->where('status', '!=', TaskStatusEnum::Cancel)
			->where('created_at', '>=', $monday2)
			->where('created_at', '<', $monday3)
			->count();
		$week2->assignTasks = Task::where('creater_id', $accId)
			->where('status', '!=', TaskStatusEnum::Done)
			->where('status', '!=', TaskStatusEnum::Cancel)
			->where('created_at', '>=', $monday2)
			->where('created_at', '<', $monday3)
			->count();
		$week2->outdateTasks = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
			->where(function ($query) {
				$query->where('task_assignees.account_id', $this->jwtAccount->id)
					->orWhere('creater_id', $this->jwtAccount->id);
			})
			->where('tasks.status', '!=', TaskStatusEnum::Cancel)
			->where('tasks.end_time', '<', $now)
			->where(function ($query) {
				$query->where('end_time', '<', DB::raw('actual_end_time'))
					->orWhereNull('actual_end_time');
			})
			->where('created_at', '>=', $monday2)
			->where('created_at', '<', $monday3)
			->count();

		// week 3
		$completedAssignedTasks = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_assignees.account_id', $accId)
			->where('status', TaskStatusEnum::Done)
			->where('created_at', '>=', $monday3)
			->where('created_at', '<', $monday4)->count();

		$completedAssignTasks = Task::where('creater_id', $accId)
			->where('status', TaskStatusEnum::Done)
			->where('created_at', '>=', $monday3)
			->where('created_at', '<', $monday4)->count();

		$week3->completedTasks = $completedAssignedTasks + $completedAssignTasks;
		$week3->assignedTasks = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_assignees.account_id', $accId)
			->where('status', '!=', TaskStatusEnum::Done)
			->where('status', '!=', TaskStatusEnum::Cancel)
			->where('created_at', '>=', $monday3)
			->where('created_at', '<', $monday4)
			->count();
		$week3->assignTasks = Task::where('creater_id', $accId)
			->where('status', '!=', TaskStatusEnum::Done)
			->where('status', '!=', TaskStatusEnum::Cancel)
			->where('created_at', '>=', $monday3)
			->where('created_at', '<', $monday4)
			->count();
		$week3->outdateTasks = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
			->where(function ($query) {
				$query->where('task_assignees.account_id', $this->jwtAccount->id)
					->orWhere('creater_id', $this->jwtAccount->id);
			})
			->where('tasks.status', '!=', TaskStatusEnum::Cancel)
			->where('tasks.end_time', '<', $now)
			->where(function ($query) {
				$query->where('end_time', '<', DB::raw('actual_end_time'))
					->orWhereNull('actual_end_time');
			})
			->where('created_at', '>=', $monday3)
			->where('created_at', '<', $monday4)
			->count();

		// week 4
		$completedAssignedTasks = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_assignees.account_id', $accId)
			->where('status', TaskStatusEnum::Done)
			->where('created_at', '>=', $monday4)
			->where('created_at', '<', $firstDayNextMonth)->count();

		$completedAssignTasks = Task::where('creater_id', $accId)
			->where('status', TaskStatusEnum::Done)
			->where('created_at', '>=', $monday4)
			->where('created_at', '<', $firstDayNextMonth)->count();

		$week4->completedTasks = $completedAssignedTasks + $completedAssignTasks;
		$week4->assignedTasks = TaskAssignee::join('tasks', 'task_assignees.task_id', '=', 'tasks.id')
			->where('task_assignees.account_id', $accId)
			->where('status', '!=', TaskStatusEnum::Done)
			->where('status', '!=', TaskStatusEnum::Cancel)
			->where('created_at', '>=', $monday4)
			->where('created_at', '<', $firstDayNextMonth)
			->count();
		$week4->assignTasks = Task::where('creater_id', $accId)
			->where('status', '!=', TaskStatusEnum::Done)
			->where('status', '!=', TaskStatusEnum::Cancel)
			->where('created_at', '>=', $monday4)
			->where('created_at', '<', $firstDayNextMonth)
			->count();
		$week4->outdateTasks = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
			->where(function ($query) {
				$query->where('task_assignees.account_id', $this->jwtAccount->id)
					->orWhere('creater_id', $this->jwtAccount->id);
			})
			->where('tasks.status', '!=', TaskStatusEnum::Cancel)
			->where('tasks.end_time', '<', $now)
			->where(function ($query) {
				$query->where('end_time', '<', DB::raw('actual_end_time'))
					->orWhereNull('actual_end_time');
			})
			->where('created_at', '>=', $monday4)
			->where('created_at', '<', $firstDayNextMonth)
			->count();

		$result->week1 = $week1;
		$result->week2 = $week2;
		$result->week3 = $week3;
		$result->week4 = $week4;

		return $this->responseResult($result);
	}

	public function getAllAssigner()
	{
		$result = array();
		try {
			$tasks = Task::join('task_assignees', 'tasks.id', '=', 'task_assignees.task_id')
				->where('task_assignees.task_role', TaskRoleEnum::Hoster)
				->where('task_assignees.account_id', $this->jwtAccount->id)
				->get();
	
			foreach ($tasks->unique('creater_id') as $task) {
				array_push($result, $task->creater);
			}
		} catch (Exception $ex) {
			Log::error($ex);
			return $this->responseResult(null, false);
		}
		return $this->responseResult($result);
	}

	public function getAllAssignee()
	{
		$result = array();
		try {
			$taskAssignees = TaskAssignee::join('tasks', 'tasks.id', '=', 'task_assignees.task_id')
				->where('task_assignees.task_role', TaskRoleEnum::Hoster)
				->where('tasks.creater_id', $this->jwtAccount->id)
				->get();
			foreach ($taskAssignees->unique('account_id') as $assignee) {
				array_push($result, $assignee->account);
			}
		} catch (Exception $ex) {
			Log::error($ex);
			return $this->responseResult(null, false);
		}
		return $this->responseResult($result);
	}
}
