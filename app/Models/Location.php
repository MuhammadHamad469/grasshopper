<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Location extends Model
{
	use LoggableEntity;
	protected $fillable = ['name', 'slug'];

	public function projects()
	{
		return $this->hasMany(Project::class);
	}

	public function getRouteKeyName()
	{
		return 'slug';
	}

	protected static function boot()
	{
		parent::boot();

		static::creating(function ($location) {
			$location->slug = $location->generateUniqueSlug($location->name);
		});
	}

	private function generateUniqueSlug($name)
	{
		$slug = Str::slug($name);
		$count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

		return $count ? "{$slug}-{$count}" : $slug;
	}
}