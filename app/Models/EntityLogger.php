<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityLogger extends Model
{
	protected $fillable = [
			'action_type',
			'entity_type',
			'entity_id',
			'entity_name',
			'description',
			'performed_by',
			'additional_details'
	];
}