<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyBillAttachment extends Model
{
	protected $guarded = [];

	protected $primaryKey = ['supply_bill_id', 'attachment_id'];

	public $incrementing = false;

	public $timestamps = false;
}
