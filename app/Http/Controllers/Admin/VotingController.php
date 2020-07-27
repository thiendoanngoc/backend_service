<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;
use App\Models\VotingTopic;
use App\Models\VotingOption;
use App\Models\VotingBinding;
use App\Models\Voter;
use Exception;
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
				}
				$item['options'] = $options;
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
		
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
			'options.*.name' => 'required|string'
		]);

		$options = $validated['options'];

		$topic = new VotingTopic();
		$topic->name = $validated['name'];
		$topic->description = $validated['description'];
		$topic->start_date = $validated['start_date'];
		$topic->end_date = $validated['end_date'];
		$topic->creater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($topic, $options) {
				$isError = !$topic->save();

				if ($isError) {
					DB::rollBack();
					return $this->responseResult(null, false);
				} else {
					foreach ($options as $item) {
						$option = new VotingOption();
						$option->name = $item['name'];
						if(!$option->save()) {
							DB::rollBack();
							return $this->responseResult(null, false);
						} else {
							$votingBinding = new VotingBinding();
							$votingBinding->voting_topic_id = $topic->id;
							$votingBinding->voting_option_id = $option->id;
							if(!$votingBinding->save()) {
								DB::rollBack();
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

	public function update(Request $request, VotingTopic $votingTopic)
	{
		$validated = $this->validateRequest([
			'option_id' => 'required|numeric'
		]);

		$accountId = $this->jwtAccount->id;
		$topicId = $votingTopic->id;
		$optionId = $validated['option_id'];

		$isTransSuccess = false;
		try {
			DB::transaction(function () use ($accountId, $topicId, $optionId) {
				$voter = new Voter();
				$checkVote = Voter::where('voting_topic_id', $topicId)
					->where('voting_option_id', $optionId)
					->where('voter_id', $accountId)
					->count();

				if($checkVote != 0) {
					return $this->responseResult(null, false);
				}
				$voter->voting_topic_id = $topicId;
				$voter->voting_option_id = $optionId;
				$voter->voter_id = $accountId;
				$isError = !$voter->save();
				
				if ($isError) {
					DB::rollBack();
					return $this->responseResult(null, false);
				}
				$isTransSuccess = true;
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);
			return $this->responseResult(null, false);
		}
		return $isTransSuccess ? $this->responseResult() : $this->responseResult(null, false);
	}

	public function destroy(VotingTopic $topic)
	{
		$topicId = $topic->id;

		$attachments = Attachment::join('contract_attachments', 'attachment_id', '=', 'id')
			->where('contract_id', $contractId)
			->get()
			->pluck('path')
			->all();
		
		try {
			DB::transaction(function () use ($topicId, $attachments) {
				try {
					$optionIds = VotingBinding::where('voting_topic_id', $topicId)
						->get()
						->pluck('voting_option_id')
						->all();

					VotingBinding::where('voting_topic_id', '=', $topicId)->delete();
					VotingOption::destroy($optionIds);
					Voter::where('voting_topic_id', '=', $topicId)->delete();
					VotingTopic::destroy($topicId);
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
