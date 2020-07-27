<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Exception;
use Illuminate\Support\Facades\Log;

class CustomerController extends Controller
{
	public function index()
	{
		$customers = Customer::orderBy('id', 'desc')->get();
		return $this->responseResult($customers);
	}

	public function show(Customer $customer)
	{
		return $this->responseResult($customer);
	}

	public function store()
	{
		$validated = $this->validateRequest([
			'full_name' => 'required|max:32',
			'email' => 'required|email:rfc,dns|max:32|unique:customers',
			'phone_number' => 'required|max:32|unique:customers',
			'address' => 'max:128',
			'birthday' => '',
			'gender_id' => 'required',
			'company_name' => ''
		]);

		$customer = new Customer();
		$customer->full_name = $validated['full_name'];
		$customer->email = strtolower($validated['email']);
		$customer->phone_number = $validated['phone_number'];
		$customer->address = $validated['address'];
		$customer->birthday = $validated['birthday'];
		$customer->gender_id = $validated['gender_id'];
		$customer->company_name = $validated['company_name'] ?? null;
		$customer->creater_id = $this->jwtAccount->id;

		try {
			if ($customer->save()) {
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

	public function update(Customer $customer)
	{
		$validated = $this->validateRequest([
			'full_name' => 'required|max:32',
			'email' => 'required|email:rfc,dns|max:32|unique:customers,email,' . $customer->id,
			'phone_number' => 'required|max:32|unique:customers,phone_number,' . $customer->id,
			'address' => 'max:128',
			'birthday' => '',
			'gender_id' => 'required',
			'company_name' => ''
		]);

		$customer->email = strtolower($validated['email']);
		$customer->phone_number = $validated['phone_number'];
		$customer->full_name = $validated['full_name'];
		$customer->address = $validated['address'];
		$customer->birthday = $validated['birthday'];
		$customer->gender_id = $validated['gender_id'];
		$customer->company_name = $validated['company_name'] ?? null;
		$customer->updater_id = $this->jwtAccount->id;

		try {
			if ($customer->save()) {
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

	public function destroy(Customer $customer)
	{
		try {
			if ($customer->delete()) {
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
}
