<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\Attachment;
use Exception;
use Illuminate\Support\Facades\Log;

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
}
