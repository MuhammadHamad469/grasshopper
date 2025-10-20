<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectType extends Model
{

	protected $table = 'project_types';


	protected $fillable = ['name, slug'];


	protected $casts = [
			'name' => 'string',
			'slug' => 'string'
	];

	public function projects(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(Project::class);
	}
}