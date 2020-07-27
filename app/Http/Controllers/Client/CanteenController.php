<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\CanteenRegistration;
use App\Models\Position;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Enums\CanteenRegistrationEnum;
use App\Enums\CanteenModeEnum;
use App\Enums\CanteenStatusEnum;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Validator;
use Illuminate\Http\JsonResponse;
use stdClass;

class CanteenController extends Controller
{
    public function index()
	{
		$canteen = CanteenRegistration::all();
		return $this->responseResult($canteen);
    }

    public function showHistory(Request $request)
	{
        $accountId = $this->jwtAccount->id;

        $validated = $this->validateRequest([
            'date' => 'date',
            'start_date' => 'date',
            'end_date' =>'date|after:start_date',
        ]);

        $date = $validated['date'] ?? null;
        

        try {
            $dateArr = [];
            if(!empty($request->input('start_date')) && !empty($request->input('end_date'))) {        
                $dateRange = new \DatePeriod(
                    new \DateTime(date('Y-m-d 00:00:00', strtotime($validated['start_date']))),
                    new \DateInterval('P1D'),
                    new \DateTime(date('Y-m-d 00:00:00', strtotime($validated['end_date'] . '+ 1 day')))
                );
        
                foreach ($dateRange as $value) {
                    $date = $value->format('Y-m-d H:i:s');
                    array_push($dateArr, $date);
                }

                $canteenArr = [];
                foreach ($dateArr as $date) {   
                    $canteenWithDate = CanteenRegistration::where('account_id', $accountId)
                        ->where('date', $date)
                        ->get()
                        ->toArray();

                    $canteenArr = array_merge($canteenArr, $canteenWithDate);
                }

                return $this->responseResult($canteenArr);
            }
            else{
                $canteen = CanteenRegistration::where('account_id', $accountId)
                    ->get();
    
                $canteenWithDate = $canteen->filter(function($models) use ($date) {
                    if($date == null) return $models->date;
                    $regex = "/^((19[0-9][0-9]|20[0-9][0-9])[-]?(0[1-9]|1[0-2])[-]?([0-2][0-9]|3[0-1]))[ ]?(([0-1][0-9]|2[0-3])[:]?([0-5][0-9])[:]?([0-5][0-9]))?$/";
                    
                    preg_match($regex, $models->date, $result);
                    $date = explode(' ', $date)[0];
                    // preg_match($regex, $date, $date);
                    return $result[1] == $date;
                    // return $result[1] == $date[1];
                })->values();
            }
        } catch (Exception $ex) {
            Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
            Log::error($ex);

            return $this->responseResult(null, false);
        }

        return $this->responseResult($canteenWithDate);
    }

    public function showDetailCanteen(CanteenRegistration $canteen)
	{
        $accountId = $this->jwtAccount->id;
        $canteenId = $canteen->id;

        try{
            $canteens = CanteenRegistration::where('account_id', $accountId)
                ->where('id', $canteenId)
                ->first();
        } catch (Exception $ex){
            Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
            Log::error($ex);

            return $this->responseResult(null, false);
        }
        return $this->responseResult($canteens);
    }

    public function countRegister(Request $request)
	{
        $data = new \stdClass();

        $validated = $this->validateRequest([
            'date' => 'date',
            'type' => 'required|numeric',
            'type' => Rule::in(CanteenRegistrationEnum::$types),
            'start_date' => 'date',
            'end_date' =>'date|after:start_date',
        ]);

        // $date = $validated['date'] ?? null;
        $type = $validated['type'];

        try{
            $dateArr = [];
            if(empty($validated['date'])) {
                if(!empty($request->input('start_date')) && !empty($request->input('end_date'))) {
                    $dateRange = new \DatePeriod(
                        new \DateTime(date('Y-m-d 00:00:00', strtotime($validated['start_date']))),
                        new \DateInterval('P1D'),
                        new \DateTime(date('Y-m-d 00:00:00', strtotime($validated['end_date'] . '+ 1 day')))
                    );
                    
                    foreach ($dateRange as $value) {
                        $date = $value->format('Y-m-d H:i:s');
                        array_push($dateArr, $date);
                    }

                    $toTal = 0;
                    foreach ($dateArr as $date) {   
                        $count = CanteenRegistration::where('date', $date)
                            ->where('type', $type)
                            ->pluck('amount')
                            ->all();
            
                        $toTal += array_sum($count);
                    }

                    $data->count = $toTal;
                    return $this->responseResult($data);

                } else {
                    $count = CanteenRegistration::where('type', $type)
                        ->sum('amount');
                }

            } else {
                $count = CanteenRegistration::where('date', $validated['date'])
                    ->where('type', $type)
                    ->sum('amount');
            }
        } catch (Exception $ex){
            Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
            Log::error($ex);

            return $this->responseResult(null, false);
        }

        
        $data->count = $count;

        return $this->responseResult($data);
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest([
            'date' => 'date',
            'start_date' => 'date',
            'end_date' =>'date|after:start_date',
            'type' => 'required',
            'type' => Rule::in(CanteenRegistrationEnum::$types),
            'amount' => 'required|min:1',
            'mode' => 'required',
            'mode' => Rule::in(CanteenModeEnum::$modes),
            'status' => 'required',
            'status' => Rule::in(CanteenStatusEnum::New),
            'note' => '',
            'name_list' => '',
        ]);
        
        try {
            $dateArr = [];
            if(empty($request->input('date'))) {
                if(!empty($request->input('start_date')) && !empty($request->input('end_date'))) {
                    $dateRange = new \DatePeriod(
                        new \DateTime(date('Y-m-d 00:00:00', strtotime($validated['start_date']))),
                        new \DateInterval('P1D'),
                        new \DateTime(date('Y-m-d 00:00:00', strtotime($validated['end_date'] . '+ 1 day')))
                    );
            
                    foreach ($dateRange as $value) {
                        $date = $value->format('Y-m-d');
                        $weekDay = date('w', strtotime($date));
        
                        if($weekDay != 0 && $weekDay != 6)
                        {
                            array_push($dateArr, $date);
                        }
                    }
                } else {
                    return $this->responseResult(null, false);
                }
            } else {
                array_push($dateArr, $validated['date']);
            }
            $accountId = $this->jwtAccount->id;
    
            foreach ($dateArr as $date) {
                $count = CanteenRegistration::where('date', '=', $date)
                    ->where('type', '=', $validated['type'])
                    ->where('mode', '=', $validated['mode'])
                    ->where('account_id', '=', $accountId)
                    ->count();
                if($count > 0) {
                    return $this->responseResult(null, false);
                }
            }
            switch ($validated['mode']) {
                case CanteenModeEnum::Personal:
                    if($validated['amount'] != 1) {
                        return $this->responseResult(null, false);
                    }
                    break;
                case CanteenModeEnum::Department:
                    $deptId = Staff::join('positions', 'staffs.position_id', 'positions.id')
                    ->where('staffs.account_id', $accountId)
                    ->pluck('positions.department_id')
                    ->first();
    
                    $countDept = Staff::join('positions', 'staffs.position_id', 'positions.id')
                    ->where('positions.department_id', $deptId)
                    ->count();
    
                    if($validated['amount'] > $countDept) {
                        return $this->responseResult(null, false);
                    }
                    break;
                case CanteenModeEnum::Guest:
                    if($validated['amount'] < 1) {
                        return $this->responseResult(null, false);
                    }
                    break;
            }
        } catch (Exception $ex) {
            return $this->responseResult(null, false, 'Internal exception');
        }

        try {
			$success = DB::transaction(function () use ($accountId, $dateArr, $validated) {
                foreach ($dateArr as $date) {
                    $canteen = new CanteenRegistration();
                    $canteen->date = $date;
                    $canteen->type = $validated['type'];
                    $canteen->amount = $validated['amount'];
                    $canteen->mode = $validated['mode'];
                    $canteen->status = $validated['status'];
                    $canteen->note = $validated['note'] ?? null;
                    $canteen->name_list = $validated['name_list'] ?? null;
                    $canteen->account_id = $accountId;
                    $canteen->creater_id = $accountId;
    
                    if (!$canteen->save()) {
                        DB::rollBack();
                        return false;
                    }
                }
                return true;
			});
		} catch (Exception $ex) {
			Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
			Log::error($ex);

			return $this->responseResult(null, false);
		}

		return $this->responseResult(null, $success);
    }

    public function destroy(CanteenRegistration $canteen)
    {
        if($canteen->creater_id != $this->jwtAccount->id) {
			return $this->responseResult(null, false);
		}
        $canteenId = $canteen->id;
        $date = $canteen->date;
        $now = new \DateTime(date('Y-m-d H:i:s', strtotime('now')));

        try {
			$success = DB::transaction(function () use ($canteenId, $now, $date) {
				try {
                    $date = new \DateTime(date('Y-m-d 16:00:00', strtotime($date . '- 1 day')));

                    if($now < $date) {
                        CanteenRegistration::destroy($canteenId);
                        return true;
                    }
                    
                    return false;
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
