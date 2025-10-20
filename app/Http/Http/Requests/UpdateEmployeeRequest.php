<?php

namespace App\Http\Requests;

class UpdateEmployeeRequest extends StoreEmployeeRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = parent::rules();

		return $rules;
	}

	/**
	 * Get the validated data from the request.
	 *
	 * @return array
	 */
	public function validated()
	{
		$validated = parent::validated();

		// For update, we don't want to overwrite existing fields with defaults
		// We only include fields that were actually submitted
		$validated = array_intersect_key($validated, $this->all());

		if ($this->hasFile('picture')) {
			$path = $this->file('picture')->store('employee_pictures', 'public');
			$validated['picture_path'] = str_replace('public/', '', $path);
		}

		return array_filter($validated);
	}
}