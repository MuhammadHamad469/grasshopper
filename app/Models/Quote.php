<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;

class Quote extends Model
{
	use LoggableEntity;

	protected $fillable = [
			'client_name',
			'client_address',
			'issue_date',
			'expiry_date',
			'quote_number',
			'vat_number',
			'company_name',
			'company_address',
			'total_amount',
			'converted_to_invoice',
	];

	protected $casts = [
			'issue_date' => 'date',
			'expiry_date' => 'date',
			'total_amount' => 'decimal:2',
	];

	// Define the relationship to QuoteItem
	public function items()
	{
		return $this->hasMany(QuoteItem::class);
	}
	public function invoice()
	{
    	return $this->belongsTo(Invoice::class);
	}
}