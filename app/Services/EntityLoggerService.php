<?php

namespace App\Services;

use App\Models\EntityLogger;
use Illuminate\Support\Facades\Auth;

class EntityLoggerService
{
	public static function log($actionType, $entity, $description, $additionalDetails = null)
	{
		return EntityLogger::create([
				'action_type' => $actionType,
				'entity_type' => class_basename($entity),
				'entity_id' => $entity->id ?? null,
				'entity_name' => self::getEntityName($entity),
				'description' => $description,
				'performed_by' => Auth::user()->name ?? 'System',
				'additional_details' => $additionalDetails ?? $entity->id,
		]);
	}

	private static function getEntityName($entity)
	{
		if (isset($entity->name)) return $entity->name;
		if (isset($entity->title)) return $entity->title;
		if (isset($entity->number)) return $entity->number;
		if (isset($entity->project_name)) return $entity->number;
		return null;
	}
}