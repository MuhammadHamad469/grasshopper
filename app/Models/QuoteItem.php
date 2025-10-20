<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Model;

class QuoteItem extends Model
{
	use LoggableEntity;
	protected $fillable = [
			'quote_id',
			'description',
			'quantity',
			'unit_price',
			'vat_rate',
			'amount',
	];

	// Define the inverse relationship to Quote
	public function quote()
	{
		return $this->belongsTo(Quote::class);
	}
}