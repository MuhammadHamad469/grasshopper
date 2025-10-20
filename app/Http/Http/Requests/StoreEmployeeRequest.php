<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true; // Authorization is handled by the controller using Gates
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
				'user_id' => 'required|exists:users,id',
				'email' => 'required|email',

			// Basic Information
				'phone_number' => 'nullable|string|max:20',
				'position' => 'nullable|string|max:100',
				'employee_type' => 'nullable|string|in:regular,contract,part_time,temporary',
				'start_date' => 'nullable|date',
				'end_date' => 'nullable|date|after_or_equal:start_date',

			// Contact Information
				'emergency_contact_name' => 'nullable|string|max:255',
				'emergency_contact_phone' => 'nullable|string|max:20',

			// Additional Personal Details
				'date_of_birth' => 'nullable|date|before:today',

			// Financial Information
				'daily_rate' => 'nullable|numeric|min:0',
				'overtime_rate' => 'nullable|numeric|min:0',
				'bank_name' => 'nullable|string|max:100',
				'bank_account_number' => 'nullable|string|max:50',
				'tax_number' => 'nullable|string|max:50',

			// Leave and Attendance Tracking
				'leave_days_allowed' => 'nullable|integer|min:0',
				'sick_days_allowed' => 'nullable|integer|min:0',

			// Picture
				'picture' => 'nullable|image|max:2048', // Max 2MB
		];
	}

	/**
	 * Get the validated data from the request.
	 *
	 * @return array
	 */
	public function validated()
	{
		$validated = parent::validated();

		// Set default values for fields that are not provided
		$defaults = [
				'employee_type' => 'regular',
				'start_date' => now(),
				'leave_days_allowed' => 10,
				'leave_days_taken' => 0,
				'sick_days_allowed' => 5,
				'sick_days_taken' => 0,
				'days_absent' => 0,
				'days_present' => 0,
				'daily_rate' => 0,
				'overtime_rate' => 0,
		];

		// Merge validated data with defaults (only for missing fields)
		foreach ($defaults as $key => $value) {
			if (!isset($validated[$key]) || $validated[$key] === null) {
				$validated[$key] = $value;
			}
		}

		// Handle picture upload if present
		if ($this->hasFile('picture')) {
			$path = $this->file('picture')->store('employee_pictures', 'public');
			$validated['picture_path'] = str_replace('public/', '', $path);
		}

		return $validated;
	}
}