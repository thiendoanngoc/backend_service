<?php

namespace App\Http\Controllers\Client;

use App\Enums\SellingStuffStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Utils\Helpers;
use App\Models\Stuff;
use App\Models\StuffAttachment; 
use App\Models\Attachment; 
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StuffController extends Controller
{
    public function getAllStuff()
    {
        $stuffs = Stuff::all();
        try {
			foreach ($stuffs as $item) {
				$id = $item->id;
				$attachments = Attachment::join('stuff_attachments', 'attachment_id', '=', 'id')
					->where('stuff_id', $id)
					->select('path')
					->get()
					->toArray();
				$tempArr = [];
				foreach($attachments as $value) {
					array_push($tempArr, $value['path']);
				}
		
				$item['attachments'] = $tempArr;
				$item->creater;
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}

        return $this->responseResult($stuffs);
    }

    public function showStuff(Stuff $stuff)
    {
        try {
			$attachments = Attachment::join('stuff_attachments', 'attachment_id', '=', 'id')
				->where('stuff_id', $stuff->id)
				->select('path')
				->get()
				->toArray();
				
			$tempArr = [];
			foreach($attachments as $item) {
				array_push($tempArr, $item['path']);
			}
			$stuff->creater;
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
		
		$stuff['attachments'] = $tempArr;
        return $this->responseResult($stuff);
    }

    public function getAllMyStuffs()
    {
        $stuffs = Stuff::where('seller_id', $this->jwtAccount->id)->orderBy('selling_status', 'asc')->get();
        try {
			foreach ($stuffs as $item) {
				$id = $item->id;
				$attachments = Attachment::join('stuff_attachments', 'attachment_id', '=', 'id')
					->where('stuff_id', $id)
					->select('path')
					->get()
					->toArray();

				$tempArr = [];
				foreach($attachments as $value) {
					array_push($tempArr, $value['path']);
				}
				$item['attachments'] = $tempArr;
				$item->creater;
			}
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}
        return $this->responseResult($stuffs);
    }

    public function storeStuff(Request $request)
    {
        $validated = $this->validateRequest([
            'name' => 'required|max:32|unique:stuffs',
			'description' => '',
			'price' => 'required|numeric',
            'selling_start' => 'required|date',
            'selling_end' => 'required|date|after:start_date',
            'attachments' => 'array|min:1'
        ]);

        $stuff = new Stuff();
        $stuff->name = $validated['name'];
		$stuff->description = $validated['description'];
		$stuff->price = $validated['price'];
        $stuff->selling_start = $validated['selling_start'];
        $stuff->selling_end = $validated['selling_end'];
        $stuff->seller_id = $this->jwtAccount->id;
        $stuff->creater_id = $this->jwtAccount->id;

        $attachments = $validated['attachments'];
		
        try{
            DB::transaction(function () use ($stuff, $attachments){
                if(!$stuff->save()){
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
							$stuffAttachment = new StuffAttachment();
							$stuffAttachment->stuff_id = $stuff->id;
							$stuffAttachment->attachment_id = $attachment->id;
							if(!$stuffAttachment->save()) {
								DB::rollBack();
								Helpers::deleteImages($attachments, false);
								return $this->responseResult(null, false);
							}
						}
					}
                }
            });
        } catch(Exception $ex){
            Log::error('message: ' . $ex->getMessage() . ',code: ' . $ex->getCode());
            Log::error($ex);

            return $this->responseResult(null, false);
        }

        return $this->responseResult();
    }

    public function updateStuff(Request $request, Stuff $stuff)
	{
		$validated = $this->validateRequest([
            'name' => 'required|max:32|unique:stuffs,name,' . $stuff->id,
			'description' => '',
			'price' => 'numeric',
            'selling_status' => '',
            'selling_start' => 'required|date',
            'selling_end' => 'required|date|after:start_date',
            'attachments' => 'array|min:1'
		]);

        $stuff->name = $validated['name'];
		$stuff->description = $validated['description'];
		$stuff->price = $validated['price'];
        $stuff->selling_start = $validated['selling_start'];
        $stuff->selling_end = $validated['selling_end'];
        if($request->input('selling_status')) {
            $stuff->selling_status = $validated['selling_status'];
        }
        $stuff->updater_id = $this->jwtAccount->id;
        
        $attachments = $validated['attachments'];

		try {
			DB::transaction(function () use ($stuff, $attachments) {
				if (!$stuff->save()) {
                    DB::rollBack();
                    Helpers::deleteImages($attachments, false);
					return $this->responseResult(null, false);
				} else {
                    // delete attachment and relation
					try {
						$attachIds = StuffAttachment::where('stuff_id', $stuff->id)
							->get()
							->pluck('attachment_id')
							->all();
                        StuffAttachment::where('stuff_id', '=', $stuff->id)->delete();
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
							$stuffAttach = new StuffAttachment();
							$stuffAttach->stuff_id = $stuff->id;
							$stuffAttach->attachment_id = $attachment->id;
							if(!$stuffAttach->save()) {
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

	public function changeStuffStatus()
	{
        $validated = $this->validateRequest([
            'stuff_id' => 'required',
            'selling_status' => 'required',
            'selling_status' => Rule::in(SellingStuffStatusEnum::$types),
        ]);
        $stuff = Stuff::find($validated['stuff_id']);
        if($this->jwtAccount->id != $stuff->seller_id || !$stuff) {
            return $this->responseResult(null, false);
        }
		$stuff->selling_status = $validated['selling_status'];
		
		DB::transaction(function () use ($stuff) {
			if (!$stuff->save()) {
                DB::rollBack();
                return $this->responseResult(null, false);
            }
		});

		return $this->responseResult();
	}

}