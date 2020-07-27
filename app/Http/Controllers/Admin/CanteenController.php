<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CanteenRegistration;
use App\Models\Position;
use App\Models\Staff;
use Illuminate\Validation\Rule;
use App\Enums\CanteenRegistrationEnum;
use App\Enums\CanteenModeEnum;
use App\Enums\CanteenStatusEnum;
use Exception;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CanteenController extends Controller
{
    public function canteenReportFilter()
	{
		$validated = $this->validateRequest([
			'department_id' => '',
			'date' => 'required|date',
			'type' => ''
		]);

		$departmentId = $validated['department_id'] ?? null;
		$date = date('Y-m-d', strtotime($validated['date']));
        $type = $validated['type'] ?? 0;
        
        if($type != 0) {
            $this->validateRequest([
                'type' => Rule::in(CanteenRegistrationEnum::$types)
            ]);
        }
        
        if($departmentId) {
            $accountArr = Staff::join('positions', 'staffs.position_id', 'positions.id')
            ->where('positions.department_id', $departmentId)
            ->pluck('staffs.account_id')
            ->toArray();
            
            $query = CanteenRegistration::whereIn('account_id', $accountArr);
        } else {
            $accountArr = Staff::join('positions', 'staffs.position_id', 'positions.id')
            ->pluck('staffs.account_id')
            ->toArray();
            
            $query = CanteenRegistration::whereIn('account_id', $accountArr);
        }

        $query = $query->where('date', $date);

        if($type != 0) {
            $query = $query->where('type', $type);
        }

        $filteredCanteen = $query->get();

        $result = array();

        foreach ($filteredCanteen as $key => $item) {
            $type = '';
            switch ($item->type) {
                case CanteenRegistrationEnum::Breakfast:
                    $type = trans('F010');
                    break;

                case CanteenRegistrationEnum::Lunch:
                    $type = trans('F011');
                    break;
            }

            $staffId = $item->account_id;
            $staff = Staff::where('account_id', $staffId)->first();
            $account = $item->register;

            $deptName = $staff->department()->name;
            $name = $account->full_name;
            $accountId = $account->id;
            $date = date('Y-m-d', strtotime($item->date));
            $created_at = date('Y-m-d h:m:s', strtotime($item->created_at));
            $amount = $item->amount;
            $note = $item->note;
            $name_list = $item->name_list;

            array_push($result, (object) ['no' => $key + 1, 'account_id' => $accountId,'name' => $name, 'department' => $deptName, 'date' => $date, 'created_at' => $created_at, 'type' => $type, 'amount' => $amount, 'note' => $note, 'name_list' => $name_list]);
        }
		return $this->responseResult(collect($result));
	}
}
