@extends('layouts.app')

@section('title', 'Team Weekly Plans - Manager Dashboard')

@section('content')
<style>
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
}

.manager-container {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

.manager-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.manager-header h1 {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
}

.manager-header p {
    color: #6b7280;
    font-size: 1.1rem;
    margin: 0.5rem 0 0 0;
}

.team-plan-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    margin-bottom: 1.5rem;
    overflow: hidden;
    position: relative;
}

.team-plan-card.approved {
    border-left: 6px solid #10b981;
}

.team-plan-card.rejected {
    border-left: 6px solid #ef4444;
}

.team-plan-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.employee-avatar {
    flex-shrink: 0;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.2rem;
    box-shadow: 0 5px 15px rgba(124, 58, 237, 0.3);
}

.tasks-preview {
    background: rgba(249, 250, 251, 0.8);
    border-radius: 15px;
    padding: 1.5rem;
    margin: 1.5rem 0;
    border: 1px solid rgba(0, 0, 0, 0.05);
    width: 100%;
    box-sizing: border-box;
}

.task-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid rgba(0, 0, 0, 0.08);
    flex-wrap: wrap;
    gap: 0.5rem;
}

.task-item:last-child {
    border-bottom: none;
}

.task-item > div:first-child {
    flex: 1;
    min-width: 0;
}

.task-item .task-title {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.9rem;
    word-break: break-word;
}

.priority-badge {
    font-size: 0.75rem;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-weight: 600;
    letter-spacing: 0.5px;
    flex-shrink: 0;
}

.priority-high {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

.priority-medium {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
}

.priority-low {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.task-status {
    font-size: 0.75rem;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-weight: 600;
    letter-spacing: 0.5px;
    flex-shrink: 0;
}

.task-status.pending {
    background: linear-gradient(135deg, #fef3c7, #fde68a);
    color: #92400e;
}

.task-status.approved {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    color: #065f46;
}

.task-status.rejected {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    color: #991b1b;
}

.btn-task {
    padding: 0.4rem 0.8rem;
    border-radius: 8px;
    font-size: 0.8rem;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}

.btn-task:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.btn-approve {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-reject {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.btn {
    padding: 0.75rem 1.5rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.btn-outline {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(0, 0, 0, 0.1);
    color: #374151;
}

.btn-success {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: white;
}

.btn-danger {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    color: white;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(15px);
    margin: auto;
    padding: 2.5rem;
    border-radius: 20px;
    width: 90%;
    max-width: 550px;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
    border: 1px solid rgba(255, 255, 255, 0.3);
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-20px); }
    to { opacity: 1; transform: translateY(0); }
}

.modal-content h3 {
    background: linear-gradient(135deg, #7c3aed 0%, #5b21b6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 1.5rem;
}

.modal-content label {
    display: block;
    margin-bottom: 0.75rem;
    font-weight: 600;
    color: #374151;
    font-size: 0.95rem;
}

.modal-content textarea {
    width: 100%;
    padding: 1rem;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.9rem;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
    resize: vertical;
    min-height: 100px;
}

.modal-content textarea:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
}

.alert {
    padding: 1rem;
    border-radius: 12px;
    margin-bottom: 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    border: 1px solid #10b981;
    color: #065f46;
}

.alert-error {
    background: linear-gradient(135deg, #fee2e2, #fecaca);
    border: 1px solid #ef4444;
    color: #991b1b;
}

.alert-info {
    background: linear-gradient(135deg, #dbeafe, #bfdbfe);
    border: 1px solid #3b82f6;
    color: #1e40af;
}

.tabs {
    display: flex;
    border-bottom: 2px solid rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 20px;
    padding: 0.5rem 1.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    flex-wrap: wrap;
}

.tab {
    padding: 1rem 1.5rem;
    cursor: pointer;
    border-bottom: 3px solid transparent;
    transition: all 0.3s ease;
    font-weight: 600;
    color: #6b7280;
    font-size: 1rem;
    flex-shrink: 0;
}

.tab.active {
    border-bottom-color: #7c3aed;
    color: #7c3aed;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.empty-state {
    text-align: center;
    padding: 3rem;
    color: #6b7280;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #d1d5db;
}

.empty-state h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.5rem;
    font-weight: 600;
}

.empty-state p {
    margin: 0;
    font-size: 1rem;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .manager-container {
        padding: 1rem;
    }
    .manager-header {
        padding: 1.5rem;
    }
    .manager-header h1 {
        font-size: 2rem;
    }
    .team-plan-card {
        padding: 1.5rem;
    }
    .task-item {
        flex-direction: column;
        align-items: flex-start;
    }
    .task-item > div:first-child {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    .task-item > div:last-child {
        width: 100%;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }
    .btn-task {
        width: auto;
    }
    .team-plan-card .flex {
        flex-wrap: wrap;
        justify-content: center;
        gap: 0.75rem;
    }
    .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="manager-container">
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> {{ session('info') }}
        </div>
    @endif

    <!-- Manager Header -->
    <div class="manager-header">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="margin: 0;">👨‍💼 Team Weekly Plans</h1>
                <p style="margin: 0.5rem 0 0 0;">Review and approve your team's weekly plans</p>
            </div>
            <div style="text-align: right; color: rgba(107,114,128,0.9);">
                <div style="font-size: 1.8rem; font-weight: 700; color: #7c3aed;">{{ $pendingPlans->count() }}</div>
                <div style="font-size: 0.9rem;">Plans Pending Review</div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <div class="tab active" onclick="showTab(event, 'pending')">
            Pending ({{ $pendingPlans->count() }})
        </div>
        <div class="tab" onclick="showTab(event, 'approved')">
            Approved ({{ $approvedPlans->count() }})
        </div>
        <div class="tab" onclick="showTab(event, 'rejected')">
            Rejected ({{ $rejectedPlans->count() }})
        </div>
    </div>

    <!-- Pending Plans Tab -->
    <div id="pending-tab" class="tab-content active">
        @forelse($pendingPlans as $plan)
            <div class="team-plan-card">
                <!-- Employee Info -->
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    <div class="employee-avatar">{{ strtoupper(substr($plan->user->name, 0, 2)) }}</div>
                    <div style="flex: 1; min-width: 150px;">
                        <h4 style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">{{ $plan->user->name }}</h4>
                        <p style="margin: 0; font-size: 0.9rem; color: #6b7280;">{{ $plan->user->email }}</p>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <span style="font-size: 0.8rem; color: #6b7280;">
                            Submitted {{ $plan->updated_at->diffForHumans() }}
                        </span>
                    </div>
                </div>

                <!-- Plan Overview -->
                <div style="margin-bottom: 1.5rem;">
                    <div style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 0.75rem;">
                        Week of {{ $plan->start_date->format('M d, Y') }}
                    </div>
                    <div style="color: #6b7280; font-size: 0.9rem; margin-bottom: 1rem;">
                        {{ $plan->start_date->format('M d') }} - {{ $plan->end_date->format('M d, Y') }}
                    </div>
                    @if($plan->summary)
                        <div style="color: #4b5563; line-height: 1.6; font-size: 0.95rem; margin-bottom: 1rem; word-break: break-word;">
                            {{ str_limit($plan->summary, 150) }}
                        </div>
                    @endif
                </div>

                <!-- Tasks Preview -->
                <div class="tasks-preview">
                    <h5 style="margin: 0 0 1rem 0; font-size: 1rem; font-weight: 700; color: #374151;">
                        Tasks ({{ $plan->tasks->count() }})
                    </h5>
                    @foreach($plan->tasks->take(3) as $task)
                        <div class="task-item">
                            <div style="flex: 1; min-width: 0;">
                                <div class="task-title">{{ $task->title }}</div>
                                <div style="display: flex; gap: 0.5rem; margin-top: 0.4rem; flex-wrap: wrap;">
                                    <span style="font-size: 0.75rem; color: #6b7280; background: #f3f4f6; padding: 0.2rem 0.6rem; border-radius: 12px; flex-shrink: 0;">
                                        {{ ucfirst($task->day) }}
                                    </span>
                                    <span class="priority-badge priority-{{ strtolower($task->priority) }}">{{ $task->priority }}</span>
                                </div>
                                @if($task->manager_feedback)
                                    <div style="font-size: 0.8rem; color: #ef4444; margin-top: 0.5rem; font-style: italic; word-break: break-word;">
                                        Feedback: {{ $task->manager_feedback }}
                                    </div>
                                @endif
                            </div>
                            <div style="display: flex; gap: 0.5rem; flex-shrink: 0;">
                                @if($task->status === 'approved')
                                    <span class="task-status approved">approved</span>
                                @elseif($task->status === 'rejected')
                                    <span class="task-status rejected">rejected</span>
                                @else
                                    <form action="{{ route('tenant.weekly-plan-tasks.approve', $task->id) }}" method="POST" style="display: inline;">
                                        {{ csrf_field() }}
                                        {{ method_field('PATCH') }}
                                        <button type="submit" class="btn-task btn-approve" title="Approve Task">✓</button>
                                    </form>
                                    <button type="button" class="btn-task btn-reject" title="Reject Task"
                                             onclick="openTaskRejectModal({{ $task->id }}, '{{ addslashes($task->title) }}')">✗</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                    @if($plan->tasks->count() > 3)
                        <div style="text-align: center; margin-top: 1rem;">
                            <a href="{{ route('tenant.weekly-plans.show', $plan->id) }}" style="font-size: 0.85rem; color: #7c3aed; font-weight: 600;">
                                View {{ $plan->tasks->count() - 3 }} more tasks
                            </a>
                        </div>
                    @endif
                </div>

                <!-- Plan Actions -->
                <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 1.5rem; border-top: 1px solid rgba(0, 0, 0, 0.08); flex-wrap: wrap; gap: 1rem;">
                    <div style="font-size: 0.9rem; color: #6b7280; flex-shrink: 0;">
                        {{ $plan->tasks->where('status', 'approved')->count() }} of {{ $plan->tasks->count() }} tasks approved
                    </div>
                    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: flex-end;">
                        <a href="{{ route('tenant.weekly-plans.show', $plan->id) }}" class="btn btn-outline">
                            <i class="fas fa-eye"></i> View Details
                        </a>
                        @php
                            $allTasksApproved = $plan->tasks->where('status', 'approved')->count() == $plan->tasks->count();
                            $hasTasks = $plan->tasks->count() > 0;
                        @endphp
                        @if($allTasksApproved && $hasTasks)
                            <form action="{{ route('tenant.weekly-plans.approve', $plan->id) }}" method="POST" style="display: inline;">
                                {{ csrf_field() }}
                                {{ method_field('PATCH') }}
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check"></i> Approve Plan
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-outline" disabled style="opacity: 0.6; cursor: not-allowed;">
                                <i class="fas fa-check"></i> Approve Plan
                            </button>
                        @endif
                        <button type="button" class="btn btn-danger" onclick="openPlanRejectModal({{ $plan->id }})">
                            <i class="fas fa-times"></i> Reject Plan
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No plans pending approval</h3>
                <p>All submitted plans have been reviewed.</p>
            </div>
        @endforelse
    </div>

    <!-- Approved Plans Tab -->
    <div id="approved-tab" class="tab-content">
        @forelse($approvedPlans as $plan)
            <div class="team-plan-card approved">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    <div class="employee-avatar" style="background: linear-gradient(135deg, #10b981, #059669);">
                        {{ strtoupper(substr($plan->user->name, 0, 2)) }}
                    </div>
                    <div style="flex: 1; min-width: 150px;">
                        <h4 style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">{{ $plan->user->name }}</h4>
                        <p style="margin: 0; font-size: 0.9rem; color: #6b7280;">{{ $plan->user->email }}</p>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <div style="font-size: 0.8rem; color: #10b981; font-weight: 700;">✓ APPROVED</div>
                        <div style="font-size: 0.8rem; color: #6b7280;">{{ $plan->approved_at->format('M d, Y') }}</div>
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 0.75rem;">
                        Week of {{ $plan->start_date->format('M d, Y') }}
                    </div>
                    <div style="color: #6b7280; font-size: 0.9rem;">
                        {{ $plan->start_date->format('M d') }} - {{ $plan->end_date->format('M d, Y') }} • {{ $plan->tasks->count() }} tasks
                    </div>
                </div>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: flex-start;">
                    <a href="{{ route('tenant.weekly-plans.show', $plan->id) }}" class="btn btn-outline">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-check-circle"></i>
                <h3>No approved plans</h3>
                <p>Approved plans will appear here.</p>
            </div>
        @endforelse
    </div>

    <!-- Rejected Plans Tab -->
    <div id="rejected-tab" class="tab-content">
        @forelse($rejectedPlans as $plan)
            <div class="team-plan-card rejected">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
                    <div class="employee-avatar" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        {{ strtoupper(substr($plan->user->name, 0, 2)) }}
                    </div>
                    <div style="flex: 1; min-width: 150px;">
                        <h4 style="margin: 0; font-size: 1.1rem; font-weight: 600; color: #1f2937;">{{ $plan->user->name }}</h4>
                        <p style="margin: 0; font-size: 0.9rem; color: #6b7280;">{{ $plan->user->email }}</p>
                    </div>
                    <div style="text-align: right; flex-shrink: 0;">
                        <div style="font-size: 0.8rem; color: #ef4444; font-weight: 700;">✗ REJECTED</div>
                        <div style="font-size: 0.8rem; color: #6b7280;">{{ $plan->rejected_at->format('M d, Y') }}</div>
                    </div>
                </div>
                <div style="margin-bottom: 1rem;">
                    <div style="font-size: 1.25rem; font-weight: 700; color: #1f2937; margin-bottom: 0.75rem;">
                        Week of {{ $plan->start_date->format('M d, Y') }}
                    </div>
                    <div style="color: #6b7280; font-size: 0.9rem; margin-bottom: 1rem;">
                        {{ $plan->start_date->format('M d') }} - {{ $plan->end_date->format('M d, Y') }} • {{ $plan->tasks->count() }} tasks
                    </div>
                    @if($plan->rejection_reason)
                        <div style="background: linear-gradient(135deg, #fee2e2, #fecaca); border: 1px solid #ef4444; border-radius: 12px; padding: 1.2rem; color: #991b1b; font-size: 0.9rem; word-break: break-word;">
                            <strong>Rejection Reason:</strong>
                            <p style="margin: 0.5rem 0 0 0;">{{ $plan->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: flex-start;">
                    <a href="{{ route('tenant.weekly-plans.show', $plan->id) }}" class="btn btn-outline">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
        @empty
            <div class="empty-state">
                <i class="fas fa-times-circle"></i>
                <h3>No rejected plans</h3>
                <p>Rejected plans will appear here.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Task Rejection Modal -->
<div id="taskRejectModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Reject Task</h3>
        <form id="taskRejectForm" method="POST">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <div style="margin-bottom: 1.5rem;">
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Task:</label>
                <p id="taskTitle" style="font-weight: 600; color: #1f2937; margin: 0; font-size: 1.1rem; word-break: break-word;"></p>
            </div>
            <div style="margin-bottom: 2rem;">
                <label for="task_manager_feedback">Feedback (Required):</label>
                <textarea id="task_manager_feedback" name="manager_feedback" rows="4"
                           placeholder="Provide specific feedback to help the employee improve..." required></textarea>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end; flex-wrap: wrap;">
                <button type="button" class="btn btn-outline" onclick="closeTaskRejectModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">Reject Task</button>
            </div>
        </form>
    </div>
</div>

<!-- Plan Rejection Modal -->
<div id="planRejectModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Reject Plan</h3>
        <form id="planRejectForm" method="POST">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <div style="margin-bottom: 2rem;">
                <label for="plan_rejection_reason">Rejection Reason (Required):</label>
                <textarea id="plan_rejection_reason" name="rejection_reason" rows="4"
                           placeholder="Explain why this plan is being rejected and what needs to be improved..." required></textarea>
            </div>
            <div style="display: flex; gap: 1rem; justify-content: flex-end; flex-wrap: wrap;">
                <button type="button" class="btn btn-outline" onclick="closePlanRejectModal()">Cancel</button>
                <button type="submit" class="btn btn-danger">Reject Plan</button>
            </div>
        </form>
    </div>
</div>

<script>
function showTab(event, tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });

    // Show selected tab content
    document.getElementById(tabName + '-tab').classList.add('active');

    // Add active class to clicked tab
    event.target.classList.add('active');
}

function openTaskRejectModal(taskId, taskTitle) {
    document.getElementById('taskTitle').textContent = taskTitle;
    document.getElementById('taskRejectForm').action = '/tenant/weekly-plan-tasks/' + taskId + '/reject';
    document.getElementById('task_manager_feedback').value = '';
    document.getElementById('taskRejectModal').style.display = 'flex';
}

function closeTaskRejectModal() {
    document.getElementById('taskRejectModal').style.display = 'none';
}

function openPlanRejectModal(planId) {
    document.getElementById('planRejectForm').action = '/tenant/weekly-plans/' + planId + '/reject';
    document.getElementById('plan_rejection_reason').value = '';
    document.getElementById('planRejectModal').style.display = 'flex';
}

function closePlanRejectModal() {
    document.getElementById('planRejectModal').style.display = 'none';
}

// Close modal when clicking outside
window.onclick = function(event) {
    var taskModal = document.getElementById('taskRejectModal');
    var planModal = document.getElementById('planRejectModal');
    if (event.target == taskModal) {
        closeTaskRejectModal();
    }
    if (event.target == planModal) {
        closePlanRejectModal();
    }
}

// Set initial active tab on load
document.addEventListener('DOMContentLoaded', function() {
    const firstTab = document.querySelector('.tabs .tab');
    const firstTabContent = document.querySelector('.tab-content');
    if (firstTab && firstTabContent && !firstTab.classList.contains('active')) {
        firstTab.classList.add('active');
        firstTabContent.classList.add('active');
    }
});
</script>

@endsection