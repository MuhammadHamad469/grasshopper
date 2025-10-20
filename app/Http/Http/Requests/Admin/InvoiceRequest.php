<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InvoiceRequest extends FormRequest
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
				'client_name' => 'required|string|max:255',
				'client_address' => 'required|string|max:255',
				'issue_date' => 'required|date',
				'expiry_date' => 'required|date|after_or_equal:issue_date',
				'total_amount' => 'required|numeric|min:0',
				'invoice_number' => [
						'required',
						'string',
						'max:255',
						Rule::unique('invoices')->ignore($this->invoice),
				],
				'vat_number' => 'required|string|max:255',
				'company_name' => 'required|string|max:255',
				'company_address' => 'required|string|max:255',
				'items' => 'required|array|min:1',
				'items.*.description' => 'required|string|max:255',
				'items.*.quantity' => 'required|integer|min:1',
				'items.*.unit_price' => 'required|numeric|min:0',
				'items.*.vat_rate' => 'required|numeric|min:0|max:100',

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
				'items.required' => 'At least one item is required for the invoice.',
				'items.*.quantity.min' => 'The quantity for each item must be at least 1.',
				'items.*.unit_price.min' => 'The unit price for each item must be at least 0.',
				'items.*.vat_rate.min' => 'The VAT rate for each item must be between 0 and 100.',
				'items.*.vat_rate.max' => 'The VAT rate for each item must be between 0 and 100.',
				'expiry_date.after_or_equal' => 'The expiry date must be equal to or after the issue date.',
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