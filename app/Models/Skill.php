<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
	use LoggableEntity;

	protected $fillable = [
			'name',
			'slug',
			'description'
	];

	/**
	 * The employees that belong to the skill.
	 */
	public function employees()
	{
		return $this->belongsToMany('App\Employee', 'employee_skills')
				->withPivot('proficiency_level')
				->withTimestamps();
	}
}