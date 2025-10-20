<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\WeeklyPlan;
use App\Models\WeeklyPlanTask;
use App\Models\{User,Employee};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Carbon\CarbonPeriod;

class WeeklyPlanController extends Controller
{
    public function __construct()
    {
        $this->moduleName = 'Weekly Plan';

        view()->share('moduleName', $this->moduleName);
    }
    /**
     * Display a listing of weekly plans based on user role
     */
    public function index()
    {
        $user = Auth::user();
        // $getEmployee = Employee::get();
        // return $getEmployee;
        $query = WeeklyPlan::query();

        try {
            if ($user->isAdmin()) {
                // Super Administrators see all plans
                $query->with(['user.teams', 'tasks', 'approver', 'rejecter']);

            } elseif ($user->isAnyManager()) {
                // Managers see plans of users in their managed teams
                $managedUsers = $user->getManagedUsers();
                if ($managedUsers->isEmpty()) {
                    // If manager has no team members, show only their own plans
                    $query->where('user_id', $user->id);
                } else {
                    // Show plans from managed users plus their own plans
                    $userIds = $managedUsers->pluck('id')->toArray();
                    $userIds[] = $user->id; // Include manager's own plans
                    $query->whereIn('user_id', $userIds);
                }

                $query->with(['user.teams', 'tasks', 'approver', 'rejecter']);
            } else {
                // Regular users see only their own plans
                $query->where('user_id', $user->id)
                    ->with(['tasks', 'approver', 'rejecter']);
            }

            $plans = $query->orderBy('created_at', 'desc')->paginate(10);

            return view('tenant.weekly-plans.index', compact('plans'));
        } catch (\Exception $e) {
            Log::error('Error in WeeklyPlanController@index: ' . $e->getMessage());

            // Fallback: show only user's own plans
            $plans = WeeklyPlan::where('user_id', $user->id)
                ->with(['tasks', 'approver', 'rejecter'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);

            return view('tenant.weekly-plans.index', compact('plans'))
                ->with('warning', 'Some team features may not be available. Please contact your administrator.');
        }

        $plans = $query->orderBy('created_at', 'desc')->get();

        return view('tenant.weekly  -plans.index', compact('plans'));
    }

    /**
     * Show the form for creating a new weekly plan
     */
    public function create()
    {
        $plan = new WeeklyPlan([
            'start_date' => now()->startOfWeek(),
            'end_date' => now()->endOfWeek(),
        ]);

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('tenant.weekly-plans.create', compact('plan', 'days'));
    }

    /**
     * Store a newly created weekly plan
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'summary' => 'required|string|max:1000',
            'tasks' => 'required|array|min:1',
            'tasks.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.description' => 'nullable|string|max:1000',
            'tasks.*.priority' => 'required|in:low,medium,high',
        ]);

        // Generate title if not provided
        $title = $validated['title'] ?: 'Week of ' . Carbon::parse($validated['start_date'])->format('M d, Y');
        $plan = WeeklyPlan::create([
            'user_id' => auth()->id(),
            'title' => $title,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'summary' => $validated['summary'],
            'status' => 'draft',
        ]);


        foreach ($validated['tasks'] as $taskData) {
            $dueDate = $this->calculateDueDate($plan->start_date, $taskData['day']);
            $plan->tasks()->create([
                'day' => $taskData['day'],
                'title' => $taskData['title'],
                'description' => $taskData['description'] ?? null,
                'due_date' => $dueDate,
                'priority' => $taskData['priority'],
                'status' => 'pending',
            ]);
        }

        return redirect()->route('tenant.weekly-plans.show', $plan->id)
            ->with('success', 'Weekly plan created successfully!');
    }

    /**
     * Display the specified weekly plan
     */
    public function show($id)
    {
        $plan = WeeklyPlan::with(['tasks', 'user.teams', 'approver', 'rejecter'])->findOrFail($id);

        // Authorization check
        // $canView = $this->canViewPlan($plan);
        // if (!$canView) {
        //     abort(403, 'Unauthorized to view this weekly plan.');
        // }

        // Ensure dates are set
        if (is_null($plan->start_date) || is_null($plan->end_date)) {
            $plan->start_date = now()->startOfWeek();
            $plan->end_date = now()->endOfWeek();
        }
        $period = [];
        $date  = Carbon::parse($plan->start_date);
        $end   = Carbon::parse($plan->end_date);
        while ($date->lte($end)) {
            $period[] = $date->copy();
            $date->addDay();
        }
        return view('tenant.weekly-plans.show', compact('plan', 'period'));
    }


    private function canViewPlan($plan)
    {
        $user = Auth::user();

        // Plan owner can always view
        if ($plan->user_id === $user->id) {
            return true;
        }

        // Admin can view any plan
        if ($user->isAdmin()) {
            return true;
        }

        // Manager can view plans from their team members
        if ($user->isManager()) {
            return $user->canManage($plan->user);
        }

        return false;
    }

    /**
     * Show the form for editing the specified weekly plan
     */
    public function edit($id)
    {
        $plan = WeeklyPlan::with('tasks')->findOrFail($id);
        // Authorization check: Only plan owner can edit
        // if ($plan->user_id !== auth()->id()) {
        //     abort(403, 'You can only edit your own weekly plans.');
        // }

        if (!in_array($plan->status, ['draft', 'rejected'])) {
            return redirect()->route('tenant.weekly-plans.show', $plan->id)
                ->with('error', 'Only draft or rejected plans can be edited.');
        }

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

        return view('tenant.weekly-plans.edit', compact('plan', 'days'));
    }

    /**
     * Update the specified weekly plan
     */
    public function update(Request $request, $id)
    {
        $plan = WeeklyPlan::findOrFail($id);

        // Authorization check: Only plan owner can update
        // if ($plan->user_id !== auth()->id()) {
        //     abort(403, 'You can only update your own weekly plans.');
        // }

        if (!in_array($plan->status, ['draft', 'rejected'])) {
            return redirect()->route('tenant.weekly-plans.show', $plan->id)
                ->with('error', 'Only draft or rejected plans can be updated.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'summary' => 'required|string|max:1000',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'tasks' => 'required|array|min:1',
            'tasks.*.title' => 'required|string|max:255',
            'tasks.*.day' => 'required|string|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'tasks.*.priority' => 'required|in:low,medium,high',
            'tasks.*.description' => 'nullable|string|max:1000',
        ]);

        // Update plan
        $plan->update([
            'title' => $validated['title'],
            'summary' => $validated['summary'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'status' => 'draft',
            'rejection_reason' => null,
            'rejected_at' => null,
            'rejected_by' => null
        ]);

        // Delete existing tasks and create new ones
        $plan->tasks()->delete();

        foreach ($validated['tasks'] as $taskData) {
            $dueDate = $this->calculateDueDate($plan->start_date, $taskData['day']);
            $plan->tasks()->create([
                'title' => $taskData['title'],
                'description' => $taskData['description'] ?? null,
                'day' => $taskData['day'],
                'priority' => $taskData['priority'],
                'due_date' => $dueDate,
                'status' => 'pending',
            ]);
        }

        return redirect()->route('tenant.weekly-plans.show', $plan->id)
            ->with('success', 'Weekly plan updated successfully!');
    }

    /**
     * Remove the specified weekly plan from storage
     */
    public function destroy($id)
    {
        $plan = WeeklyPlan::findOrFail($id);

        // Authorization check: Only plan owner or manager can delete
        if ($plan->user_id !== auth()->id() && !auth()->user()->isManager()) {
            abort(403, 'You can only delete your own weekly plans.');
        }

        // If user is not a manager, only allow deletion of draft plans
        if (!auth()->user()->isManager() && $plan->status !== 'draft') {
            return redirect()->back()
                ->with('error', 'Only draft plans can be deleted.');
        }

        $plan->delete();

        return redirect()->route('tenant.weekly-plans.index')
            ->with('success', 'Weekly plan deleted successfully!');
    }

    /**
     * Submit a weekly plan for approval
     */
    public function submit($id)
    {
        $plan = WeeklyPlan::findOrFail($id);

        // Authorization check: Only plan owner can submit
        // if ($plan->user_id !== auth()->id()) {
        //     abort(403, 'You can only submit your own weekly plans.');
        // }

        if ($plan->status !== 'draft') {
            return redirect()->back()->with('error', 'Only draft plans can be submitted.');
        }

        if ($plan->tasks()->count() === 0) {
            return redirect()->back()->with('error', 'Cannot submit a plan without tasks.');
        }

        $plan->update([
            'status' => 'submitted',
            'submitted_at' => now()
        ]);

        return redirect()->route('tenant.weekly-plans.show', $plan->id)
            ->with('success', 'Weekly plan submitted for approval!');
    }

    /**
     * Manager dashboard showing team weekly plans
     */
    public function managerDashboard()
    {
        $user = Auth::user();

        // Check if user is a manager
        if (!$user->isAnyManager()) {
            abort(403, 'Access denied. Manager privileges required.');
        }

        try {
            // Get managed users (excluding the manager themselves)
            $managedUsers = $user->getManagedUsers();

            // If manager has no team members, show empty dashboard
            if ($managedUsers->isEmpty()) {
                return view('tenant.weekly-plans.manager-dashboard', [
                    'pendingPlans' => collect([]),
                    'approvedPlans' => collect([]),
                    'rejectedPlans' => collect([]),
                    'teamInfo' => $user->managedTeams->first()
                ]);
            }

            $managedUserIds = $managedUsers->pluck('id')->toArray();

            // Fetch plans by status (excluding manager's own plans)
            $pendingPlans = WeeklyPlan::where('status', 'submitted')
                ->whereIn('user_id', $managedUserIds)
                ->with(['user.teams', 'tasks'])
                ->orderBy('updated_at', 'desc')
                ->get();

            $approvedPlans = WeeklyPlan::where('status', 'approved')
                ->whereIn('user_id', $managedUserIds)
                ->with(['user.teams', 'tasks', 'approver'])
                ->orderBy('approved_at', 'desc')
                ->take(10)
                ->get();

            $rejectedPlans = WeeklyPlan::where('status', 'rejected')
                ->whereIn('user_id', $managedUserIds)
                ->with(['user.teams', 'tasks', 'rejecter'])
                ->orderBy('rejected_at', 'desc')
                ->take(10)
                ->get();

            return view('tenant.weekly-plans.manager-dashboard', compact(
                'pendingPlans',
                'approvedPlans',
                'rejectedPlans'
            ));
        } catch (\Exception $e) {
            Log::error('Error in WeeklyPlanController@managerDashboard: ' . $e->getMessage());

            return view('tenant.weekly-plans.manager-dashboard', [
                'pendingPlans' => collect([]),
                'approvedPlans' => collect([]),
                'rejectedPlans' => collect([]),
                'teamInfo' => null
            ])->with('error', 'Unable to load team data. Please contact your administrator.');
        }
    }

    /**
     * Approve a weekly plan
     */
    public function approve($id)
    {
        $plan = WeeklyPlan::with('user.teams')->findOrFail($id);

        // Authorization check: Only manager of the plan owner can approve
        if (!Auth::user()->canApprove($plan)) {
            abort(403, 'You can only approve plans from your team members.');
        }

        if ($plan->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted plans can be approved.');
        }

        // Check if all tasks are approved
        $pendingTasks = $plan->tasks()->where('status', '!=', 'approved')->count();
        if ($pendingTasks > 0) {
            return redirect()->back()->with('error', 'All tasks must be approved before approving the plan.');
        }

        $plan->update([
            'status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->id(),
            'rejection_reason' => null,
            'rejected_at' => null,
            'rejected_by' => null
        ]);

        return redirect()->back()->with('success', 'Plan approved successfully!');
    }

    /**
     * Reject a weekly plan
     */
    public function reject(Request $request, $id)
    {
        $plan = WeeklyPlan::with('user.teams')->findOrFail($id);

        // Authorization check: Only manager of the plan owner can reject
        if (!Auth::user()->canReject($plan)) {
            abort(403, 'You can only reject plans from your team members.');
        }

        if ($plan->status !== 'submitted') {
            return redirect()->back()->with('error', 'Only submitted plans can be rejected.');
        }

        $this->validate($request, [
            'rejection_reason' => 'required|string|min:10|max:500'
        ]);

        $plan->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejected_at' => now(),
            'rejected_by' => auth()->id(),
            'approved_at' => null,
            'approved_by' => null
        ]);

        return redirect()->back()->with('success', 'Plan rejected with feedback.');
    }

    /**
     * Calculate due date based on start date and day of week
     */
    private function calculateDueDate($startDate, $day)
    {
        $startOfWeek = Carbon::parse($startDate)->startOfWeek();
        $dayMap = [
            'monday' => 0,
            'tuesday' => 1,
            'wednesday' => 2,
            'thursday' => 3,
            'friday' => 4,
            'saturday' => 5,
            'sunday' => 6,
        ];
        return $startOfWeek->addDays($dayMap[strtolower($day)]);
    }

    /**
     * Check if current user can view a specific plan
     */


    /**
     * Get team statistics for manager dashboard
     */
    public function getTeamStats()
    {
        $user = Auth::user();

        if (!$user->isAnyManager()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $managedUsers = $user->getManagedUsers();
        $managedUserIds = $managedUsers->pluck('id')->toArray();

        $stats = [
            'total_team_members' => count($managedUserIds),
            'pending_plans' => WeeklyPlan::where('status', 'submitted')->whereIn('user_id', $managedUserIds)->count(),
            'approved_this_week' => WeeklyPlan::where('status', 'approved')
                ->whereIn('user_id', $managedUserIds)
                ->whereBetween('approved_at', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'overdue_submissions' => WeeklyPlan::where('status', 'draft')
                ->whereIn('user_id', $managedUserIds)
                ->where('end_date', '<', now())
                ->count(),
        ];

        return response()->json($stats);
    }
}
