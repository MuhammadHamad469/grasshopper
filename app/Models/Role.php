<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Role
 *
 * @package App
 * @property string $title
 */
class Role extends Model
{
	use LoggableEntity;
	const ADMIN = 1;
	const USER = 2;
	const TEAM_ADMIN = 3;
	const TEAM_LEADER = 4;

	protected $fillable = ['title'];
	protected $hidden = [];


}