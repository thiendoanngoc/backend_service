<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;
use App\Models\Supply;
use App\Models\SupplyBill;
use App\Models\SupplyBillAttachment;
use App\Models\Attachment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SupplyController extends Controller
{
	public function getAllSupplies()
	{
		$supplies = Supply::all();
		
		return $this->responseResult($supplies);
	}

	public function showSupply(Supply $supply)
	{
		$supplyId = $supply->id;

		return $this->responseResult($supply);
	}

	public function storeSupply(Request $request)
	{
		$validated = $this->validateRequest([
			'name' => 'required|max:32|unique:supplies',
			'price' => 'required|numeric|min:1000'
		]);

		$supply = new Supply();
		$supply->name = $validated['name'];
		$supply->price = $validated['price'];
		$supply->creater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($supply) {
				if (!$supply->save()) {
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

	public function updateSupply(Request $request, Supply $supply)
	{
		$validated = $this->validateRequest([
			'name' => 'required|max:32|unique:supplies,name,' . $supply->id,
			'price' => 'required|numeric|min:1000'
		]);

		$supply->name = $validated['name'];
		$supply->price = $validated['price'];
		$supply->updater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($supply) {
				if (!$supply->save()) {
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

	public function destroySupply(Supply $supply)
	{
		$supplyId = $supply->id;
		
		DB::transaction(function () use ($supplyId) {
			try {
				Supply::destroy($supplyId);
			} catch (Exception $e) {
				Log::error('message: ' . $e->getMessage() . ', code: ' . $e->getCode());
				Log::error($e);
				DB::rollBack();
				return $this->responseResult(null, false);
			}
		});

		return $this->responseResult();
	}

	public function getAllSupplyBills()
	{
		$supplyBills = SupplyBill::all();

		try {
			foreach ($supplyBills as $item) {
				$billId = $item->id;
				$supplyId = $item->supply_id;

				$supply = Supply::find($supplyId);

				$attachments = Attachment::join('supply_bill_attachments', 'attachment_id', '=', 'id')
					->where('supply_bill_id', $billId)
					->select('path')
					->get()
					->toArray();
	
				$item['supply'] = $supply;

				$item['attachments'] = $attachments;
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
		
		return $this->responseResult($supplyBills);
	}

	public function showSupplyBill(SupplyBill $supplyBill)
	{
		$billId = $supplyBill->id;
		$supplyId = $supplyBill->supply_id;
		try {
			$supply = Supply::find($supplyId);

			$attachments = Attachment::join('supply_bill_attachments', 'attachment_id', '=', 'id')
				->where('supply_bill_id', $billId)
				->select('path')
				->get()
				->toArray();
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
		
		$supplyBill['supply'] = $supply;

		$supplyBill['attachments'] = $attachments;

		return $this->responseResult($supplyBill);
	}

	public function storeSupplyBill(Request $request)
	{
		$validated = $this->validateRequest([
			'supply_id' => 'required|exists:supplies,id',
			'amount' => 'required|numeric|min:1',
			'attachments' => isset($request->attachments) ? 'array|min:1' : ''
		]);

		$attachments = isset($request->attachments) ? $validated['attachments'] : array();
		$supplyPrice = Supply::find($validated['supply_id'])->price;

		$supplyBill = new SupplyBill();
		$supplyBill->supply_id = $validated['supply_id'];
		$supplyBill->amount = $validated['amount'];
		$supplyBill->total = $validated['amount'] * $supplyPrice;
		$supplyBill->creater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($supplyBill, $attachments) {
				$isError = !$supplyBill->save();

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
							$supplyBillAttach = new SupplyBillAttachment();
							$supplyBillAttach->supply_bill_id = $supplyBill->id;
							$supplyBillAttach->attachment_id = $attachment->id;
							if(!$supplyBillAttach->save()) {
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

	public function updateSupplyBill(Request $request, SupplyBill $supplyBill)
	{
		$validated = $this->validateRequest([
			'supply_id' => 'required|exists:supplies,id',
			'amount' => 'required|numeric|min:1',
			'attachments' => isset($request->attachments) ? 'array|min:1' : ''
		]);

		$supplyBillId = $supplyBill->id;
		$attachments = isset($request->attachments) ? $validated['attachments'] : array();
		$supplyPrice = Supply::find($validated['supply_id'])->price;

		$supplyBill->supply_id = $validated['supply_id'];
		$supplyBill->amount = $validated['amount'];
		$supplyBill->total = $validated['amount'] * $supplyPrice;
		$supplyBill->updater_id = $this->jwtAccount->id;

		try {
			DB::transaction(function () use ($supplyBill, $attachments, $supplyBillId) {
				$isError = !$supplyBill->save();
				
				if ($isError) {
					DB::rollBack();
					Helpers::deleteImages($attachments, false);
					return $this->responseResult(null, false);
				} else {
					// delete attachment and relation
					try {
						$attachIds = SupplyBillAttachment::where('supply_bill_id', $supplyBillId)
							->get()
							->pluck('attachment_id')
							->all();
						SupplyBillAttachment::where('supply_bill_id', '=', $supplyBillId)->delete();
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
							$supplyBillAttach = new SupplyBillAttachment();
							$supplyBillAttach->supply_bill_id = $supplyBill->id;
							$supplyBillAttach->attachment_id = $attachment->id;
							if(!$supplyBillAttach->save()) {
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

	public function destroySupplyBill(SupplyBill $supplyBill)
	{
		$supplyBillId = $supplyBill->id;

		$attachments = Attachment::join('supply_bill_attachments', 'attachment_id', '=', 'id')
			->where('supply_bill_id', $supplyBillId)
			->get()
			->pluck('path')
			->all();
		
		try {
			DB::transaction(function () use ($supplyBillId, $attachments) {
				try {
					$attachIds = SupplyBillAttachment::where('supply_bill_id', $supplyBillId)
						->get()
						->pluck('attachment_id')
						->all();
					SupplyBillAttachment::where('supply_bill_id', '=', $supplyBillId)->delete();
					Attachment::destroy($attachIds);
					SupplyBill::destroy($supplyBillId);
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
