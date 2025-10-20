<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}
	
	protected function prepareForValidation()
	{
		$this->merge([
				'quote_check' => $this->has('quote_check') ? 1 : 0,
				'quote_id' => $this->input('quote_id') ?: null,
		]);
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
				'project_name' => 'required|string|max:255',
				'project_type_id' => 'required|exists:project_types,id',
				'startDate' => 'required|date',
				'endDate' => 'required|date|after_or_equal:startDate',
				'description' => 'required|string',
				'budget' => 'required|numeric|min:0',
				'actual_budget' => 'nullable|numeric|min:0',
				'status' => 'required|in:1,2,3',
				'location_id' => 'required|exists:locations,id',
				'team_leader_user_id' => 'required|exists:users,id',
				'target_hectares' => 'nullable|numeric|min:0',
				'actual_hectares' => 'nullable|numeric|min:0',
				'number_of_students' => 'nullable|integer|min:0',
				'facilitator_name' => 'nullable|string|max:255',
				'quote_check' => 'boolean',
				'inspection_check' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'labour_report_check' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'safety_talk_check' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'herbicide_check' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'invoice_check' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'facilitation_check' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'assessment_check' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'moderation_check' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'database_admin_check' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'certification_check' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
				'quote_id' => 'nullable',
				'planned_days' => 'nullable|numeric|min:0',
				'hectares_per_day' => 'nullable|numeric|min:0',
				'total_budget' => 'nullable|numeric|min:0',
				'smme_id' => 'nullable|string',
				'vehicle_kms_target' => 'nullable|integer',
				'actual_vehicle_kms' => 'nullable|integer',
				'assets' => 'nullable|array',
				'assets.*' => 'exists:assets,id',
		];
	}
}