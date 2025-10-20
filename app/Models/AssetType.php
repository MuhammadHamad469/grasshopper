<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;

class AssetType extends Model
{
	use LoggableEntity;

	protected $fillable = [
			'name',
			'description',
	];

	public function assets()
	{
		return $this->hasMany(Asset::class);
	}
}