<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyPlanTask extends Model
{
    protected $fillable = [
        'weekly_plan_id',
        'title',
        'description',
        'day',
        'due_date',
        'priority',
        'status',
        'manager_feedback',
        'approved_at',
        'rejected_at',
        'approved_by',
        'rejected_by',
    ];

    protected $casts = [
        'due_date' => 'date',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    public function weeklyPlan()
    {
        return $this->belongsTo(WeeklyPlan::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejecter()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    // Check if task is approved (completed status means approved)
    public function isApproved()
    {
        return $this->status === 'completed';
    }

    // Check if task is rejected
    public function isRejected()
    {
        return $this->status === 'rejected'; // Changed to explicitly check for 'rejected' status
    }

    // Get status for display
    public function getDisplayStatus()
    {
        if ($this->isApproved()) {
            return 'approved';
        } elseif ($this->isRejected()) {
            return 'rejected';
        } else {
            return 'pending';
        }
    }
}
