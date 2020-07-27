<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAttendee extends Model
{
    protected $guarded = [];

	protected $primaryKey = ['meeting_session_id', 'attendee_id'];

	public $incrementing = false;

	public $timestamps = false;
}
