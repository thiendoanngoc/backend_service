<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $guarded = [];

    protected $hidden = ['creater_id', 'updater_id', 'created_at', 'updated_at'];
}
