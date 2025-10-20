<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
	use LoggableEntity;

	const IN_USE = 'In use';
	const AVAILABLE = 'Not in use';
	const IN_SERVICE = 'In service';

	protected $fillable = [
			'name',
			'serial_number',
			'model',
			'status',
			'cost',
			'location',
			'purchase_date',
			'warranty_date',
			'asset_type_id',
			'project_id',
			'picture_paths',
	];

	protected $casts = [
			'purchase_date' => 'date',
			'warranty_date' => 'date',
			'picture_paths' => 'array',
			'cost' => 'decimal:2',
	];

	public function assetType()
	{
		return $this->belongsTo(AssetType::class);
	}

	public function project()
	{
		return $this->belongsTo(Project::class);
	}
}