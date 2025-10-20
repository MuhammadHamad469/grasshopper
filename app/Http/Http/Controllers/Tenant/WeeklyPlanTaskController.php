<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\WeeklyPlanTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WeeklyPlanTaskController extends Controller
{
    public function approve($id)
    {
        $task = WeeklyPlanTask::with('weeklyPlan.user')->findOrFail($id);

        // Ensure the current user is authorized to approve this task via the canManageTask helper
        if (!$this->canManageTask($task)) {
            abort(403, 'You are not authorized to approve this task.');
        }

        if ($task->status === 'approved') { // Changed from 'completed' to 'approved'
            return redirect()->back()->with('error', 'Task is already approved.');
        }

        $task->update([
            'status' => 'approved', // Changed from 'completed' to 'approved'
            'manager_feedback' => null, // Clear any previous feedback
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejected_at' => null,
            'rejected_by' => null
        ]);

        return redirect()->back()->with('success', 'Task approved successfully!');
    }

    public function reject(Request $request, $id)
    {
        $task = WeeklyPlanTask::with('weeklyPlan.user')->findOrFail($id);

        // Ensure the current user is authorized to reject this task via the canManageTask helper
        if (!$this->canManageTask($task)) {
            abort(403, 'You are not authorized to reject this task.');
        }

        $this->validate($request, [
            'manager_feedback' => 'required|string|min:10|max:500'
        ]);

        if ($task->status === 'approved') { // Changed from 'completed' to 'approved'
            return redirect()->back()->with('error', 'Cannot reject an approved task.');
        }

        $task->update([
            'status' => 'rejected',
            'manager_feedback' => $request->manager_feedback,
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
            'approved_at' => null,
            'approved_by' => null
        ]);

        return redirect()->back()->with('success', 'Task rejected with feedback.');
    }

    /**
     * Check if current user can manage (approve/reject) the task
     * This method now relies on the canManage method in the User model.
     */
    private function canManageTask($task)
    {
        // Ensure relationships are loaded
        if (!$task->relationLoaded('weeklyPlan') || !$task->weeklyPlan->relationLoaded('user')) {
            $task->load('weeklyPlan.user');
        }

        // Check if current user can manage the task's plan owner using the User model's canManage method
        return Auth::user()->canManage($task->weeklyPlan->user);
    }
}
