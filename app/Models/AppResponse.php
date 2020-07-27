<?php

namespace App\Models;

class AppResponse
{
	public $message;
	public $code;
	public $result;
	public $data;

	public function __construct($data = null, $result = true, $message = null, $code = 200)
	{
		$this->data = $data;
		$this->result = $result;
		$this->code = $code;
		$this->message = $message ? $message : ($this->result ? trans('I001') : trans('E002'));
	}
}
