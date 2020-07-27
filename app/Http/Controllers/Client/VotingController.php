<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;
use App\Models\VotingTopic;
use App\Models\VotingOption;
use App\Models\VotingBinding;
use App\Models\Voter;
use App\Models\Attachment;
use App\Models\Staff;
use App\Models\Department;
use App\Enums\VotingTargetTypeEnum;
use App\Enums\NotificationTypeEnum;
use App\Models\Notification;
use Exception;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VotingController extends Controller
{
	public function index()
	{
		$accountId = $this->jwtAccount->id;
		$votingTopics = VotingTopic::all();

		try {
			foreach ($votingTopics as $item) {
				$id = $item->id;
				$options = VotingOption::join('voting_bindings', 'voting_option_id', '=', 'id')
					->where('voting_topic_id', $id)
					->select('id', 'name')
					->get();
	
				foreach ($options as $option) {
					$isVoted = Voter::where('voting_topic_id', $id)
						->where('voting_option_id', $option->id)
						->where('voter_id', $accountId)
						->count();
					
					$option['voted'] = $isVoted != 0;

					$votingTopicIdWon = VotingTopic::where('id', $id)->first();
					$array = explode(',', $votingTopicIdWon->winning_option_id);

					if(in_array($option->id, $array)) {
						$option['won'] = true;
					}else {
						$option['won'] = false;
					}
				}
				$item['options'] = $options;
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}

		$votingTopics = $votingTopics->sortByDesc('updated_at')->values();
		
		return $this->responseResult($votingTopics);
	}

	public function show(VotingTopic $votingTopic)
	{
		$accountId = $this->jwtAccount->id;
		$topicId = $votingTopic->id;

		try {
			$options = VotingOption::join('voting_bindings', 'voting_option_id', '=', 'id')
				->where('voting_topic_id', $topicId)
				->select('id', 'name')
				->get();

			foreach ($options as $option) {
				$isVoted = Voter::where('voting_topic_id', $topicId)
					->where('voting_option_id', $option->id)
					->where('voter_id', $accountId)
					->count();
					
				$option['voted'] = $isVoted != 0;

				$votingTopicIdWon = VotingTopic::where('id', $topicId)->first();
				$array = explode(',', $votingTopicIdWon->winning_option_id);

				if(in_array($option->id, $array)) {
					$option['won'] = true;
				}else {
					$option['won'] = false;
				}
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
		
		$votingTopic['options'] = $options;
		return $this->responseResult($votingTopic);
	}

	public function store(Request $request)
	{
		$validated = $this->validateRequest([
			'name' => 'required',
			'description' => '',
			'start_date' => 'required|date',
			'end_date' => 'required|date|after:start_date',
			'options' => 'array|min:2',
			'options.*.name' => 'required|string',
			'target_type' => 'required',
			'target_type' => Rule::in(VotingTargetTypeEnum::$types),
			'target_ids' => 'array',
		]);

		$targetIds = $validated['target_ids'] ?? [];

		$currAccId = $this->jwtAccount->id;
		$targetIdsStr = null;
		$targetIdsArr = [];
		$currAccLvl = Staff::where('account_id', $currAccId)->first()->position->level;
		$currAccDept = Staff::where('account_id', $currAccId)->first()->department();

		try {
			switch ($validated['target_type']) {
				case VotingTargetTypeEnum::All:
					// only level 1 can add all staffs
					if($currAccLvl != 1) {
						return $this->responseResult(null, false, 'You do not have permission to add all staffs');
					}
					$staffIds = Staff::pluck('account_id')->toArray();

					if(!in_array($currAccId, $staffIds)) {
						array_push($staffIds, $currAccId);
					}
					$targetIdsArr = $staffIds;
					break;
				case VotingTargetTypeEnum::Department:
					$accountIds = [];
					if($currAccLvl == 1) { // level 1 user
						if(count($targetIds) > 0) {	// in case specify department
							// check department existance
							$count = Department::whereIn('id', $targetIds)
							->count();
	
							if($count != count($targetIds)) {
								return $this->responseResult(null, false, 'Invalid department id');
							}
	
							foreach ($targetIds as $deptId) {
								$deptAccIds = collect(Department::find($deptId)->accounts())
									->pluck('id')
									->toArray();
	
								$accountIds = array_merge($accountIds, $deptAccIds);
							}
						} else { // in case not specify department
							$accountIds = collect($currAccDept->accounts())
								->pluck('id')
								->toArray();
						}
					} else { // level > 1 users
						$accountIds = collect($currAccDept->accounts())
							->pluck('id')
							->toArray();
					}
					

					if(!in_array($currAccId, $accountIds)) {
						array_push($accountIds, $currAccId);
					}
					$targetIdsArr = $accountIds;
					break;
				case VotingTargetTypeEnum::Specific:
					if($currAccLvl == 1) {
						// check staff existance
						$count = Staff::whereIn('account_id', $targetIds)
							->count();

						if($count != count($targetIds)) {
							return $this->responseResult(null, false, 'Invalid account id');
						}

						$specificIds = $targetIds;
					} else {
						// check same department
						$query = Staff::whereIn('account_id', $targetIds);
						$staffs = $query->get();
						foreach ($staffs as $staff) {
							$dept = $staff->department();
							if($dept->id != $currAccDept->id) {
								return $this->responseResult(null, false, 'Only allow specifying account in the same department');
							}
						}

						$count = $query->count();
						if($count != count($targetIds)) {
							return $this->responseResult(null, false, 'Invalid account id');
						}

						$specificIds = $targetIds;
					}
					

					if(!in_array($currAccId, $specificIds)) {
						array_push($specificIds, $currAccId);
					}
					$targetIdsArr = $specificIds;
					break;
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);
			return $this->responseResult(null, false, trans('E001'));
		}
		
		$targetIdsStr = Helpers::stringifyIdList($targetIdsArr);
		$options = $validated['options'];

		$topic = new VotingTopic();
		$topic->name = $validated['name'];
		$topic->description = $validated['description'] ?? null;
		$topic->start_date = $validated['start_date'];
		$topic->end_date = $validated['end_date'];
		$topic->target_type = $validated['target_type'];
		$topic->target_users = $targetIdsStr;
		$topic->creater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($topic, $options, $targetIdsArr, $currAccId) {
				$isError = !$topic->save();

				if ($isError) {
					DB::rollBack();
					return $this->responseResult(null, false, trans('E001'));
				} else {
					foreach ($options as $item) {
						$option = new VotingOption();
						$option->name = $item['name'];
						if(!$option->save()) {
							DB::rollBack();
							return $this->responseResult(null, false, trans('E001'));
						} else {
							$votingBinding = new VotingBinding();
							$votingBinding->voting_topic_id = $topic->id;
							$votingBinding->voting_option_id = $option->id;
							if(!$votingBinding->save()) {
								DB::rollBack();
								return $this->responseResult(null, false, trans('E001'));
							}

							$diffArr = array_diff($targetIdsArr, array($currAccId));
							$data = array('voting_id' => $topic->id);
							$topic->notifyAllStakeHolders($this->connection, $topic->name, $diffArr, trans('I015'), $data);

							Notification::createNotification($diffArr, NotificationTypeEnum::Voting, $topic->id, trans('I015'), $topic->name);
						}
					}
				}
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false, trans('E001'));
		}

		return $this->responseResult();
	}

	public function update(Request $request, VotingTopic $votingTopic)
	{
		$validated = $this->validateRequest([
			'name' => 'required',
			'description' => '',
			'start_date' => 'required|date',
			'end_date' => 'required|date|after:start_date'
		]);

		$currAccId = $this->jwtAccount->id;

		$votingTopic->name = $validated['name'];
		$votingTopic->description = $validated['description'] ?? null;
		$votingTopic->start_date = $validated['start_date'];
		$votingTopic->end_date = $validated['end_date'];
		$votingTopic->updater_id = $this->jwtAccount->id;

		try {
			$success = DB::transaction(function () use ($votingTopic, $currAccId) {
				$isError = !$votingTopic->save();

				if ($isError) {
					DB::rollBack();
					return false;
				}

				$targetIdsArr = Helpers::toArrayIdsString($votingTopic->target_users);

				$diffArr = array_diff($targetIdsArr, array($currAccId));
				$data = array('voting_id' => $votingTopic->id);
				$votingTopic->notifyAllStakeHolders($this->connection, $votingTopic->name, $diffArr, trans('I016'), $data);

				Notification::createNotification($diffArr, NotificationTypeEnum::Voting, $votingTopic->id, trans('I016'), $votingTopic->name);

				return true;
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}

		return $this->responseResult(null, $success);
	}

	public function vote(Request $request)
	{
		$validated = $this->validateRequest([
			'topic_id' => 'required',
			'option_id' => ''
		]);

		$votingTopic = VotingTopic::find($validated['topic_id']);

		if(!$votingTopic) {
            return $this->responseResult(null, false, 'Not found voting topic');
        }

		$options = VotingBinding::where('voting_topic_id', $votingTopic->id)
			->get()
			->pluck('voting_option_id')
			->all();
		
		// in case voting, check option id
		if($request->input('option_id')) {
			if(!in_array($validated['option_id'], $options)) {
				return $this->responseResult(null, false, 'Invalid selected option');
			}
		}

		$accountId = $this->jwtAccount->id;
		$topicId = $votingTopic->id;
		$optionId = $request->input('option_id');
		$targetIds = Helpers::toArrayIdsString($votingTopic->target_users);

		if(!in_array($accountId, $targetIds)) {
			return $this->responseResult(null, false, 'You do not have permission to vote or un-vote');
		}

		// in case un-vote, check if current user have voted yet
		if(!$optionId) {
			$voter = Voter::where('voting_topic_id', '=', $topicId)
				->where('voter_id', $accountId)
				->first();
			if(!$voter) {
				return $this->responseResult(null, false, 'You have not voted yet');
			}
		}

		try {
			$isTransSuccess = DB::transaction(function () use ($accountId, $topicId, $optionId, $votingTopic) {
				$isError = false;
				if($optionId) {
					// vote/switch vote
					Voter::where('voting_topic_id', $topicId)
						->where('voter_id', $accountId)
						->delete();
	
					$voter = new Voter();
					$voter->voting_topic_id = $topicId;
					$voter->voting_option_id = $optionId;
					$voter->voter_id = $accountId;
					$isError = !$voter->save();
				} else {
					// un-vote
					$isError = !Voter::where('voting_topic_id', '=', $topicId)
						->where('voter_id', $accountId)->delete();
				}

				// calculate current winning option
				$totalVote = Voter::select('voting_option_id', DB::raw('COUNT(voting_option_id) as voteCount'))
					->where('voting_topic_id', $topicId)
					->groupBy('voting_option_id')
					->orderBy('voteCount', 'DESC')
					->get();

				$winningIdsStr = null;
				if(count($totalVote) > 0) {
					$maxVotedCount = $totalVote[0];

					$tempArr = array();
					array_push($tempArr, $maxVotedCount->voting_option_id);
					foreach ($totalVote->slice(1) as $vote) {
						if($maxVotedCount->voteCount == $vote->voteCount) {
							array_push($tempArr, $vote->voting_option_id);
						}
					}
					$winningIdsStr = implode(',', $tempArr);
				}
				

				$votingTopic->winning_option_id = $winningIdsStr;
				$isError &= !$votingTopic->save();
				
				if ($isError) {
					DB::rollBack();
					return false;
				}
				return true;
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);
			return $this->responseResult(null, false, trans('E001'));
		}
		return $isTransSuccess ? $this->responseResult() : $this->responseResult(null, false);
	}

	public function destroy(VotingTopic $topic)
	{
		if($topic->creater_id != $this->jwtAccount->id) {
			return $this->responseResult(null, false);
		}
		$topicId = $topic->id;
		
		try {
			$success = DB::transaction(function () use ($topicId) {
				try {
					$optionIds = VotingBinding::where('voting_topic_id', $topicId)
						->get()
						->pluck('voting_option_id')
						->all();

					if(
						!VotingBinding::where('voting_topic_id', '=', $topicId)->delete() ||
						!Voter::where('voting_topic_id', '=', $topicId)->delete() ||
						!VotingOption::destroy($optionIds) ||
						!VotingTopic::destroy($topicId)
					) {
						DB::rollBack();
						return false;
					}
					return true;
				} catch (Exception $e) {
					Log::error('message: ' . $e->getMessage() . ', code: ' . $e->getCode());
					Log::error($e);
					DB::rollBack();
					return false;
				}
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);
			return $this->responseResult(null, false);
		}

		return $this->responseResult(null, $success);
	}

	public function unVote(Request $request)
	{
		$validated = $this->validateRequest([
			'topic_id' => 'required'
		]);

		$votingTopic = VotingTopic::find($validated['topic_id']);

		if(!$votingTopic) {
            return $this->responseResult(null, false, 'Not found voting topic');
        }

		$accountId = $this->jwtAccount->id;
		$topicId = $votingTopic->id;
		$targetIds = Helpers::toArrayIdsString($votingTopic->target_users);

		if(!in_array($accountId, $targetIds)) {
			return $this->responseResult(null, false, 'You do not have permission to un-vote');
		}

		$voter = Voter::where('voting_topic_id', '=', $topicId)
			->where('voter_id', $accountId)
			->first();
		if(!$voter) {
			return $this->responseResult(null, false, 'You have not voted yet');
		}
		
		try {
			$success = DB::transaction(function () use ($topicId, $accountId, $votingTopic) {
				try {
					if(
						!Voter::where('voting_topic_id', '=', $topicId)
							->where('voter_id', $accountId)->delete()
					) {
						DB::rollBack();
						return false;
					}

					// calculate current winning option
					$totalVote = Voter::select('voting_option_id', DB::raw('COUNT(voting_option_id) as voteCount'))
						->where('voting_topic_id', $topicId)
						->groupBy('voting_option_id')
						->orderBy('voteCount', 'DESC')
						->get();

					$winningIdsStr = null;
					if(count($totalVote) > 0) {
						$maxVotedCount = $totalVote[0];

						$tempArr = array();
						array_push($tempArr, $maxVotedCount->voting_option_id);
						foreach (array_slice($totalVote, 1) as $vote) {
							if($maxVotedCount->voteCount == $vote->voteCount) {
								array_push($tempArr, $vote->voting_option_id);
							}
						}
						$winningIdsStr = implode(',', $tempArr);
					}
					

					$votingTopic->winning_option_id = $winningIdsStr;
					if(!$votingTopic->save()) {
						DB::rollBack();
						return false;
					}

					return true;
				} catch (Exception $e) {
					Log::error('message: ' . $e->getMessage() . ', code: ' . $e->getCode());
					Log::error($e);
					DB::rollBack();
					return false;
				}
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);
			return $this->responseResult(null, false);
		}

		return $this->responseResult(null, $success);
	}
}
