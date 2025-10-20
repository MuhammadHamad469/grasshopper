<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
	use LoggableEntity;
	/**
	 * @var mixed
	 */
	protected $fillable = [
			'name',
			'db_host',
			'db_port',
			'db_name',
			'db_username',
			'db_password',
			'is_active',
			'last_activity',
	];
}