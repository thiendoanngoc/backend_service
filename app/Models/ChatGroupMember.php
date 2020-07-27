<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatGroupMember extends Model
{
	protected $guarded = [];

	protected $primaryKey = ['chat_group_id', 'account_id'];

	public $incrementing = false;

	public $timestamps = false;
}
