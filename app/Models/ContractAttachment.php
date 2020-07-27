<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractAttachment extends Model
{
	protected $guarded = [];

	protected $primaryKey = ['contract_id', 'attachment_id'];

	public $incrementing = false;

	public $timestamps = false;
}
