<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
	// Why add this? Bug framework in Model class this line
	// return $this->table ?? Str::snake(Str::pluralStudly(class_basename($this)));
	protected $table = 'staffs';

	protected $guarded = [];

	protected $primaryKey = 'account_id';

	public $incrementing = false;

	public $timestamps = false;

	public function position()
	{
		return $this->belongsTo(Position::class);
	}

	public function department()
	{
		$departmentId = $this->position->department_id;
		return Department::find($departmentId);
	}
}
