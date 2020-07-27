<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StuffAttachment extends Model
{
    protected $guarded = [];

	protected $primaryKey = ['stuff_id', 'attachment_id'];

	public $incrementing = false;

	public $timestamps = false;
}
