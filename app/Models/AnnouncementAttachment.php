<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementAttachment extends Model
{
	protected $guarded = [];

	protected $primaryKey = ['announcement_id', 'attachment_id'];

	public $incrementing = false;

	public $timestamps = false;
}
