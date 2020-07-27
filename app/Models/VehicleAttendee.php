<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleAttendee extends Model
{
    protected $guarded = [];

	protected $primaryKey = ['vehicle_session_id', 'attendee_id'];

	public $incrementing = false;

	public $timestamps = false;
}
