<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ClientRequest extends FormRequest
{
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize(): bool
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		$rules = [
			'name'        => 'required|string|max:255',
			'db_host'     => 'required|string|max:255',
			'db_port'     => 'required|string|max:255',
			'db_name'     => 'required|string|max:255',
			'db_username' => 'required|string|max:255',
			'db_password' => 'required|string|max:255',
			'is_active'   => 'required|numeric',

		];

		return $rules;
	}

	/**
	 * Get custom messages for validator errors.
	 *
	 * @return array
	 */
	public function messages(): array
	{
		return [
			'name.required'        => 'The Client Name is required',
			'db_host.required'     => 'The Db Host is required',
			'db_port.required'     => 'The Db Port is required',
			'db_name.required'     => 'The Db Name is required',
			'db_username.required' => 'The Db Username is required',
			'db_password.required' => 'The Db Password is required',
			'is_active.required'   => 'The Client is Active is required',
		];
	}

	/**
	 * Prepare the data for validation.
	 *
	 * @return void
	 */
	protected function prepareForValidation()
	{
		//
	}
}