<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
	protected $guarded = [];

	protected $primaryKey = ['task_id', 'attachment_id'];

	public $incrementing = false;

	public $timestamps = false;

	public function attachment()
	{
		return $this->belongsTo(Attachment::class);
	}
}
