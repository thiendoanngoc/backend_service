<?php

namespace App\Http\Controllers\Client;

use App\Models\Account;
use App\Models\Staff;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CommonController extends Controller
{
    public function searchStaff()
    {
        $validated = $this->validateRequest([
            'name' => '',
            'page' => 'min:1|numeric',
            'per_page' => 'min:1|numeric'
        ]);

        $page = $validated['page'];
        $per_page = $validated['per_page'];
        $skip = ($page - 1) * $per_page;

        $currAccId = $this->jwtAccount->id;
		$currAccLvl = Staff::where('account_id', $currAccId)->first()->position->level;
        $currAccDept = Staff::where('account_id', $currAccId)->first()->department();

        try{
            if($currAccLvl == 1){
                $listAccount = Account::where('accounts.full_name', 'like', '%' . $validated['name'] . '%')
                    ->skip($skip)
                    ->take($per_page)
                    ->select('id', 'full_name')
                    ->get();
            }
            else{
                $listAccount = Account::join('staffs', 'accounts.id', '=', 'staffs.account_id')
                    ->join('positions', 'positions.id', '=', 'staffs.position_id')
                    ->where('positions.department_id', '=', $currAccDept->id)
                    ->where('accounts.full_name', 'like', '%' . $validated['name'] . '%')
                    ->skip($skip)
                    ->take($per_page)
                    ->select('accounts.id', 'accounts.full_name')
                    ->get();
            }
        } catch (\Exception $ex){
            Log::error('message: ' . $ex->getMessage() . ', code: ' . $ex->getCode());
            Log::error($ex);

            return $this->responseResult(null, false);
        }

        return $this->responseResult($listAccount);
    }
}
