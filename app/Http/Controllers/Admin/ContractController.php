<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;
use App\Models\Contract;
use App\Models\ContractAttachment;
use App\Models\Attachment;
use App\Enums\ContractStatusEnum;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class ContractController extends Controller
{
	public function index()
	{
		$contracts = Contract::all();

		try {
			foreach ($contracts as $item) {
				$id = $item->id;
				$attachments = Attachment::join('contract_attachments', 'attachment_id', '=', 'id')
					->where('contract_id', $id)
					->select('path')
					->get()
					->toArray();

				$tempArr = [];
				foreach($attachments as $value) {
					array_push($tempArr, $value['path']);
				}

				$item['attachments'] = $tempArr;
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
		
		return $this->responseResult($contracts);
	}

	public function show(Contract $contract)
	{
		$contractId = $contract->id;
		try {
			$attachments = Attachment::join('contract_attachments', 'attachment_id', '=', 'id')
				->where('contract_id', $contractId)
				->select('path')
				->get()
				->toArray();
			$tempArr = [];
			foreach($attachments as $item) {
				array_push($tempArr, $item['path']);
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
		
		$contract['attachments'] = $tempArr;
		return $this->responseResult($contract);
	}

	public function store(Request $request)
	{
		$validated = $this->validateRequest([
			'name' => 'required|unique:contracts',
			'description' => '',
			'start_date' => 'required|date',
			'end_date' => 'required|date|after:start_date',
			'attachments' => 'array'
		]);

		$attachments = $validated['attachments'];

		$contract = new Contract();
		$contract->name = $validated['name'];
		$contract->description = isset($validated['description']) ? $validated['description'] : '';
		$contract->status = ContractStatusEnum::InProgress;
		$contract->start_date = $validated['start_date'];
		$contract->end_date = $validated['end_date'];
		$contract->creater_id = $this->jwtAccount->id;
		try {
			DB::transaction(function () use ($contract, $attachments) {
				$isError = !$contract->save();

				if ($isError) {
					DB::rollBack();
					Helpers::deleteImages($attachments, false);
					return $this->responseResult(null, false);
				} else {
					foreach ($attachments as $item) {
						$attachment = new Attachment();
						$attachment->path = $item; 
						if(!$attachment->save()) {
							DB::rollBack();
							Helpers::deleteImages($attachments, false);
							return $this->responseResult(null, false);
						} else {
							$contractAttach = new ContractAttachment();
							$contractAttach->contract_id = $contract->id;
							$contractAttach->attachment_id = $attachment->id;
							if(!$contractAttach->save()) {
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

		// $contract->notifyToAllUser(trans('I005'));

		return $this->responseResult();
	}

	public function update(Request $request, Contract $contract)
	{
		$validated = $this->validateRequest([
			'name' => 'required|unique:contracts,name,' . $contract->id,
			'description' => '',
			'status' => 'required|numeric',
			'status' => Rule::in(ContractStatusEnum::$types),
			'start_date' => 'required|date',
			'end_date' => 'required|date|after:start_date',
			'attachments' => 'array'
		]);

		$contractId = $contract->id;
		$attachments = $validated['attachments'];

		$contract->name = $validated['name'];
		$contract->description = isset($validated['description']) ? $validated['description'] : '';
		$contract->status = $validated['status'];
		$contract->start_date = $validated['start_date'];
		$contract->end_date = $validated['end_date'];
		$contract->updater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($contract, $attachments, $contractId) {
				$isError = !$contract->save();
				
				if ($isError) {
					DB::rollBack();
					Helpers::deleteImages($attachments, false);
					return $this->responseResult(null, false);
				} else {
					// delete attachment and relation
					try {
						$attachIds = ContractAttachment::where('contract_id', $contractId)
							->get()
							->pluck('attachment_id')
							->all();
						ContractAttachment::where('contract_id', '=', $contractId)->delete();
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
						$attachment->path = $item;
						if(!$attachment->save()) {
							DB::rollBack();
							Helpers::deleteImages($attachments, false);
							return $this->responseResult(null, false);
						} else {
							$contractAttach = new ContractAttachment();
							$contractAttach->contract_id = $contract->id;
							$contractAttach->attachment_id = $attachment->id;
							if(!$contractAttach->save()) {
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

	public function destroy(Contract $contract)
	{
		$contractId = $contract->id;

		$attachments = Attachment::join('contract_attachments', 'attachment_id', '=', 'id')
			->where('contract_id', $contractId)
			->get()
			->pluck('path')
			->all();
		
		try {
			DB::transaction(function () use ($contractId, $attachments) {
				try {
					$attachIds = ContractAttachment::where('contract_id', $contractId)
						->get()
						->pluck('attachment_id')
						->all();
					ContractAttachment::where('contract_id', '=', $contractId)->delete();
					Attachment::destroy($attachIds);
					Contract::destroy($contractId);
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
