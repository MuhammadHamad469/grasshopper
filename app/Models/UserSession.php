<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $fillable = [
    	'login_time',
    	'logout_time',
    	'duration_seconds',
    	'user_id',
    ];
}
