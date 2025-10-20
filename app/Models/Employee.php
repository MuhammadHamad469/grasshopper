<?php

namespace App\Models;

use App\Traits\LoggableEntity;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
	use SoftDeletes, LoggableEntity;

	/**
	 * @var array
	 */
	protected $fillable = [
			'user_id',
			'employee_id ',
			'first_name', 
			'last_name',
			'id_number',
			'gender',
			'phone_number',
			'email',
			'street_address', 
			'suburb', 
			'city', 
			'postal_code',
			'emergency_contact_name',
			'emergency_contact_phone',
			'date_of_birth',
			'position',
			'employee_type',
			'daily_rate',
			'overtime_rate',
			'days_absent',
			'days_present',
			'leave_days_allowed',
			'leave_days_taken',
			'sick_days_allowed',
			'sick_days_taken',
			'annual_leave_balance', 
			'sick_leave_balance', 
			'compassionate_leave_balance',
			'bank_name', 
			'bank_account_number', 
			'branch_code', 
			'monthly_salary', 
			'tax_number',
			'bio',
			'picture_path',
			'start_date',
			'end_date',
	];

	/**
	 * @var array
	 */
	protected $casts = [
			'date_of_birth' => 'date',
			'start_date' => 'date',
			'end_date' => 'date',
			'daily_rate' => 'decimal:2',
			'overtime_rate' => 'decimal:2',
	];

	/**
	 * Get the user that owns the employee.
	 */
	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function skills()
	{
		return $this->belongsToMany('App\Skill', 'employee_skills')
				->withPivot('proficiency_level')
				->withTimestamps();
	}

	/**
	 * @return int
	 */
	public function leaveRequests()
{
    return $this->hasMany(LeaveRequest::class)->latest();
}

public function getRemainingAnnualLeaveAttribute()
{
    return $this->annual_leave_entitlement - $this->annual_leave_taken;
}

public function getRemainingSickDaysAttribute()
{
    return $this->sick_days_allowed - $this->sick_days_taken;
}

public function getRemainingCompassionateLeaveAttribute()
{
    return $this->compassionate_days_allowed - $this->compassionate_days_taken;
}


	/**
	 * Check if employee is a contractor.
	 *
	 * @return bool
	 */
	public function isContractor(): bool
	{
		return $this->employee_type === 'contractor';
	}


	public function isRegular(): bool
	{
		return $this->employee_type === 'regular';
	}

	/**
	 * Scope a query to only include regular employees.
	 *
	 * @param Builder $query
	 * @return Builder
	 */
	public function scopeRegular(Builder $query): Builder
	{
		return $query->where('employee_type', 'regular');
	}

	/**
	 * Scope a query to only include contractors.
	 *
	 * @param  Builder  $query
	 * @return Builder
	 */
	public function scopeContractor($query): Builder
	{
		return $query->where('employee_type', 'contractor');
	}
	public function getFullNameAttribute()
{
    return "{$this->first_name} {$this->last_name}";
}

// Helper method to get full address
public function getFullAddressAttribute()
{
    $parts = [
        $this->street_address,
        $this->suburb,
        $this->city,
        $this->postal_code
    ];
    
    return implode(', ', array_filter($parts));
}
}