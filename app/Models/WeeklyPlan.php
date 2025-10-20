<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WeeklyPlan extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'start_date',
        'end_date',
        'summary',
        'status',
        'rejection_reason',
        'approved_at',
        'approved_by',
        'rejected_at',
        'rejected_by'
    ];


    protected $dates = [
        'start_date',
        'end_date',
        'approved_at',
        'rejected_at',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tasks()
    {
        return $this->hasMany(WeeklyPlanTask::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'gray',
            'submitted' => 'blue',
            'approved' => 'green',
            'rejected' => 'red'
        ];
        return $colors[$this->status] ?? 'gray';
    }

    public function canBeSubmitted()
    {
        return $this->status === 'draft' && $this->tasks()->count() > 0;
    }

    public function canBeApproved()
    {
        return $this->status === 'submitted' && 
               $this->tasks()->where('status', 'completed')->count() === $this->tasks()->count() &&
               $this->tasks()->count() > 0;
    }

    public function getFormattedDateRangeAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return 'Date not set';
        }
        return $this->start_date->format('M d') . ' - ' . $this->end_date->format('M d, Y');
    }

    /**
     * Create a default plan structure for a user
     */
    public static function defaultPlan($user)
    {
        return new static([
            'user_id' => $user->id,
            'start_date' => now()->startOfWeek(),
            'end_date' => now()->endOfWeek(),
            'status' => 'draft'
        ]);
    }
}
