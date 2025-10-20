<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;

class Smmes extends Model
{
	use LoggableEntity;

	protected $fillable = [
			'name',
			'registration_number',
			'years_of_experience',
			'team_composition',
			'grade',
			'status',
			'documents_verified',
			'company_registration', 
			'tax_certificate',      
			'bee_certificate',      
			'company_profile', 
	];

	public function projects()
	{
		return $this->hasMany(Project::class);
	}
}