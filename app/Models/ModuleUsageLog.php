<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleUsageLog extends Model
{
    protected $fillable = [
    	'user_id',
    	'module_name',
    	'start_time',
    	'end_time',
    	'duration_seconds',
    ];
}
