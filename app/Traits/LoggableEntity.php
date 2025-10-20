<?php

namespace App\Traits;

use App\Services\EntityLoggerService;

trait LoggableEntity
{
	protected static function bootLoggableEntity()
	{
		static::created(function ($model) {
			$modelName = self::getNameOrId($model);
			EntityLoggerService::log(
					'added',
					$model,
					"{$modelName} created"
			);
		});

		static::updated(function ($model) {
			$changes = $model->getDirty();
			$originalValues = $model->getOriginal();
			$changeDescriptions = [];

			foreach ($changes as $attribute => $newValue) {
				// Skip updated_at timestamp changes
				if ($attribute === 'updated_at') {
					continue;
				}

				// Get the original value
				$oldValue = $originalValues[$attribute] ?? null;

				// Format the change description based on attribute type
				if (is_bool($newValue)) {
					$changeDescriptions[] = sprintf(
							"%s changed from %s to %s",
							str_replace('_', ' ', $attribute),
							$oldValue ? 'true' : 'false',
							$newValue ? 'true' : 'false'
					);
				} elseif (is_null($oldValue)) {
					$changeDescriptions[] = sprintf(
							"%s set to \"%s\"",
							str_replace('_', ' ', $attribute),
							$newValue
					);
				} elseif (is_null($newValue)) {
					$changeDescriptions[] = sprintf(
							"%s cleared (was \"%s\")",
							str_replace('_', ' ', $attribute),
							$oldValue
					);
				} else {
					$changeDescriptions[] = sprintf(
							"%s changed from \"%s\" to \"%s\"",
							str_replace('_', ' ', $attribute),
							$oldValue,
							$newValue
					);
				}
			}

			// If there are changes to log
			if (!empty($changeDescriptions)) {
				$modelName = self::getNameOrId($model);
				EntityLoggerService::log(
						'updated',
						$model,
						"{$modelName} updated",
						implode(', ', $changeDescriptions),
						[
								'changes' => array_combine(
										array_keys($changes),
										array_map(function ($attribute) use ($changes, $originalValues) {
											return [
													'old' => $originalValues[$attribute] ?? null,
													'new' => $changes[$attribute]
											];
										}, array_keys($changes))
								)
						]
				);
			}
		});

		static::deleted(function ($model) {
			$modelName = self::getNameOrId($model);
			EntityLoggerService::log(
					'deleted',
					$model,
					"{$modelName} deleted"
			);
		});
	}

	/**
	 * Get the loggable attributes for the model.
	 * Override this method in your model to specify which attributes should be logged.
	 *
	 * @return array
	 */
	public function getLoggableAttributes()
	{
		// By default, log all fillable attributes
		return $this->fillable;
	}

	/**
	 * @param $model
	 * @return mixed
	 */
	static private function getNameOrId($entity): string
	{
		if (isset($entity->name)) return $entity->name;
		if (isset($entity->invoice_number)) return $entity->invoice_number;
		if (isset($entity->quote_number)) return $entity->quote_number;
		if (isset($entity->title)) return $entity->title;
		if (isset($entity->number)) return $entity->number;
		if (isset($entity->project_name)) return $entity->project_name;
		return $entity->id;
	}
}