<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
	use LoggableEntity;

	protected $fillable = ['name', 'slug'];

	/**
	 * Users with this permission
	 */
	public function users()
	{
		return $this->belongsToMany(User::class, 'user_permissions');
	}
}