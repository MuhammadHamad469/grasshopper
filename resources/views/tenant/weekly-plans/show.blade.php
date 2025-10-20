@extends('layouts.app')
@section('title', 'Weekly Plan')
@section('content')
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8fafc;
            margin: 0;
            padding: 0;
        }

        .plan-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .top-action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .plan-header {
            background: linear-gradient(135deg, #216592 0%, #1a5278 100%);
            color: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 6px rgba(33, 101, 146, 0.2);
        }

        .plan-header h1 {
            margin: 0 0 0.5rem 0;
            font-size: 2rem;
            font-weight: 600;
        }

        .date-range {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .btn-modern {
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-outline {
            background: white;
            border: 2px solid #e2e8f0;
            color: #6c757d;
        }

        .btn-outline:hover {
            border-color: #007bff;
            color: #007bff;
            text-decoration: none;
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
            color: white;
        }

        .btn-danger-modern {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .btn-primary-modern:hover,
        .btn-success-modern:hover,
        .btn-danger-modern:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            text-decoration: none;
            color: white;
        }

        .info-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border-left: 4px solid #007bff;
        }

        .info-card-title {
            font-size: 0.875rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.05em;
        }

        .info-card-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1a202c;
        }

        .summary-card {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .summary-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 1rem;
        }

        .summary-text {
            color: #4a5568;
            line-height: 1.6;
        }

        .schedule-section {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 2rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .days-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .day-card {
            background: #f8fafc;
            border-radius: 12px;
            overflow: hidden;
            border: 2px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .day-card.has-tasks {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0, 123, 255, 0.1);
        }

        .day-header {
            background: linear-gradient(135deg, #216592 0%, #1a5278 100%);
            color: white;
            padding: 1rem;
            font-weight: 600;
        }

        .day-date {
            padding: 0.75rem 1rem;
            font-size: 0.875rem;
            color: #6c757d;
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }

        .day-tasks {
            padding: 1rem;
        }

        .task-item {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 0.75rem;
            border-left: 4px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .task-item:last-child {
            margin-bottom: 0;
        }

        .task-title {
            font-weight: 600;
            color: #1a202c;
            margin-bottom: 0.5rem;
        }

        .task-description {
            color: #6c757d;
            font-size: 0.875rem;
            margin-bottom: 0.75rem;
        }

        .task-meta {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .priority-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .priority-high {
            background: #fed7d7;
            color: #c53030;
        }

        .priority-medium {
            background: #feebc8;
            color: #dd6b20;
        }

        .priority-low {
            background: #c6f6d5;
            color: #2f855a;
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-pending {
            background: #bee3f8;
            color: #2b6cb0;
        }

        .status-in_progress {
            background: #fbb6ce;
            color: #b83280;
        }

        .status-completed {
            background: #c6f6d5;
            color: #2f855a;
        }

        .status-draft {
            background: #e2e8f0;
            color: #4a5568;
        }

        .status-submitted {
            background: #bee3f8;
            color: #2c5282;
        }

        .status-approved {
            background: #c6f6d5;
            color: #22543d;
        }

        .status-rejected {
            background: #fed7d7;
            color: #742a2a;
        }

        .empty-state {
            text-align: center;
            color: #6c757d;
            font-style: italic;
            padding: 2rem;
        }

        .tasks-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .table-modern {
            width: 100%;
            border-collapse: collapse;
        }

        .table-modern th {
            background: #f7fafc;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #2d3748;
            border-bottom: 2px solid #e2e8f0;
        }

        .table-modern td {
            padding: 1rem;
            border-bottom: 1px solid #e2e8f0;
            color: #4a5568;
        }

        .table-modern tr:hover {
            background: #f7fafc;
        }

        /* Modal Specific Styles */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1000;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            background-color: rgba(0, 0, 0, 0.6);
            /* Black w/ opacity */
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
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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

        .btn-task {
            padding: 0.4rem 0.8rem;
            border-radius: 8px;
            font-size: 0.8rem;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
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

        /* Alerts */
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

        @media (max-width: 768px) {
            .plan-container {
                padding: 10px;
            }

            .plan-header {
                padding: 1.5rem;
            }

            .plan-header h1 {
                font-size: 1.5rem;
            }

            .top-action-bar {
                flex-direction: column;
                align-items: stretch;
            }

            .days-grid {
                grid-template-columns: 1fr;
            }

            .info-cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <div class="plan-container">
        <!-- Top Action Bar -->
        <div class="top-action-bar">
            <div>
                <a href="{{ route('tenant.weekly-plans.index') }}" class="btn-modern btn-outline"><i
                        class="fas fa-arrow-left"></i> Back to List</a>
            </div>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                @if ($plan->status == 'draft')
                    <a href="{{ route('tenant.weekly-plans.edit', $plan->id) }}" class="btn-modern btn-primary-modern"><i
                            class="fas fa-edit"></i> Edit Plan</a>
                    <form action="{{ route('tenant.weekly-plans.submit', $plan->id) }}" method="POST"
                        style="display: inline;">
                        @csrf
                        {{ method_field('PATCH') }}
                        <button type="submit" class="btn-modern btn-success-modern"><i class="fas fa-paper-plane"></i>
                            Submit for Approval</button>
                    </form>
                @endif
                @if ($plan->status == 'rejected' && Auth::user()->id === $plan->user_id)
                    <a href="{{ route('tenant.weekly-plans.edit', $plan->id) }}" class="btn-modern btn-primary-modern"><i
                            class="fas fa-redo"></i> Revise & Resubmit</a>
                @endif
                {{-- Manager Approval/Rejection Buttons for the Plan --}}
                {{-- Ensure $plan->user is a User model instance before calling canManage --}}
                @if ($plan->user && Auth::user()->canManage($plan->user) && $plan->status === 'submitted')
                    @php
                        $allTasksApproved = $plan->tasks->where('status', 'approved')->count() == $plan->tasks->count();
                        $hasTasks = $plan->tasks->count() > 0;
                    @endphp
                    @if ($allTasksApproved && $hasTasks)
                        <form action="{{ route('tenant.weekly-plans.approve', $plan->id) }}" method="POST"
                            style="display: inline;">
                            {{ csrf_field() }}
                            {{ method_field('PATCH') }}
                            <button type="submit" class="btn-modern btn-success-modern"><i class="fas fa-check"></i>
                                Approve Plan</button>
                        </form>
                    @else
                        <button type="button" class="btn-modern btn-outline" disabled
                            style="opacity: 0.6; cursor: not-allowed;"><i class="fas fa-check"></i> Approve Plan</button>
                    @endif
                    <button type="button" class="btn-modern btn-danger-modern"
                        onclick="openPlanRejectModal({{ $plan->id }})"><i class="fas fa-times"></i> Reject Plan</button>
                @endif

                {{-- NEW: Delete Button --}}
                @php
                    $canDeletePlan =
                        $plan->user_id === auth()->id() ||
                        (Auth::user()->canManage($plan->user) && $plan->user) ||
                        Auth::user()->isAdmin();
                @endphp
                @if ($canDeletePlan)
                    <button type="button" class="btn-modern btn-danger-modern"
                        onclick="openDeletePlanModal({{ $plan->id }}, '{{ addslashes($plan->title) }}')">
                        <i class="fas fa-trash"></i> Delete Plan
                    </button>
                @endif
            </div>
        </div>

        <!-- Header -->
        <div class="plan-header">
            <h1>📅 Weekly Plan</h1>
            <div class="date-range">{{ $plan->start_date->format('M d, Y') }} – {{ $plan->end_date->format('M d, Y') }}
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-error"><i class="fas fa-exclamation-triangle"></i> {{ session('error') }}</div>
        @endif
        @if (session('info'))
            <div class="alert alert-info"><i class="fas fa-info-circle"></i> {{ session('info') }}</div>
        @endif

        <!-- Info Cards -->
        <div class="info-cards">
            <div class="info-card">
                <div class="info-card-title">Employee</div>
                <div class="info-card-value">{{ $plan->user->name ?? auth()->user()->name }}</div>
            </div>
            <div class="info-card">
                <div class="info-card-title">Status</div>
                <div class="info-card-value"><span
                        class="status-badge status-{{ strtolower($plan->status) }}">{{ ucfirst($plan->status) }}</span>
                </div>
            </div>
            <div class="info-card">
                <div class="info-card-title">Total Tasks</div>
                <div class="info-card-value">{{ $plan->tasks->count() }}</div>
            </div>
            <div class="info-card">
                <div class="info-card-title">Created</div>
                <div class="info-card-value">{{ $plan->created_at->format('M d, Y') }}</div>
            </div>
        </div>

        <!-- Summary -->
        <div class="summary-card">
            <div class="summary-title">📝 Weekly Summary & Goals</div>
            <div class="summary-text">{{ $plan->summary ?? 'No summary provided for this week.' }}</div>
            @if ($plan->rejection_reason)
                <div class="alert alert-error" style="margin-top: 1.5rem;"><strong>Rejection Reason:</strong>
                    {{ $plan->rejection_reason }}</div>
            @endif
        </div>

        <!-- Weekly Schedule -->
        <div class="schedule-section">
            <div class="section-title"><i class="fas fa-calendar-week"></i> Weekly Schedule</div>

            <div class="days-grid">
                @foreach ($period as $date)
                    @php
                        $day = $date->format('l'); // Full day name, e.g. Monday
                        $dayTasks = $plan->tasks->filter(function ($task) use ($day) {
                            return strtolower($task->day) === strtolower($day);
                        });
                        $hasTasks = $dayTasks->isNotEmpty();
                    @endphp

                    <div class="day-card {{ $hasTasks ? 'has-tasks' : '' }}">
                        <div class="day-header">{{ $day }}</div>
                        <div class="day-date">{{ $date->format('M d, Y') }}</div>

                        <div class="day-tasks">
                            @if ($dayTasks->isEmpty())
                                <div class="empty-state">No tasks scheduled</div>
                            @else
                                @foreach ($dayTasks as $task)
                                    <div class="task-item">
                                        <div class="task-title">{{ $task->title }}</div>
                                        @if ($task->description)
                                            <div class="task-description">{{ $task->description }}</div>
                                        @endif
                                        <div class="task-meta">
                                            <span
                                                class="priority-badge priority-{{ strtolower($task->priority) }}">{{ $task->priority }}</span>
                                            <span
                                                class="status-badge status-{{ strtolower($task->status) }}">{{ str_replace('_', ' ', $task->status) }}</span>
                                        </div>

                                        @if ($task->status === 'rejected' && $task->manager_feedback)
                                            <div
                                                style="font-size:0.85rem;color:#ef4444;margin-top:0.75rem;font-style:italic;border-left:3px solid #ef4444;padding-left:0.5rem;">
                                                <strong>Feedback:</strong> {{ $task->manager_feedback }}
                                            </div>
                                        @endif

                                        @if ($plan->user && Auth::user()->canManage($plan->user) && $plan->status === 'submitted')
                                            <div style="margin-top:1rem;display:flex;gap:0.5rem;">
                                                @if ($task->status === 'approved')
                                                    <span class="status-badge status-approved">Approved</span>
                                                @elseif($task->status === 'rejected')
                                                    <span class="status-badge status-rejected">Rejected</span>
                                                @else
                                                    <form
                                                        action="{{ route('tenant.weekly-plan-tasks.approve', $task->id) }}"
                                                        method="POST" style="display:inline-block;">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn-task btn-approve"
                                                            title="Approve Task">
                                                            <i class="fas fa-check"></i> Approve
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn-task btn-reject" title="Reject Task"
                                                        onclick="openTaskRejectModal({{ $task->id }}, '{{ addslashes($task->title) }}')">
                                                        <i class="fas fa-times"></i> Reject
                                                    </button>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- All Tasks Table -->
        <div class="tasks-table">
            <div class="section-title" style="padding: 1.5rem 1.5rem 0;"><i class="fas fa-list"></i> All Tasks Overview
            </div>
            <table class="table-modern">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Description</th>
                        <th>Day</th>
                        <th>Priority</th>
                        <th>Status</th>
                        {{-- Ensure $plan->user is a User model instance before calling canManage --}}
                        @if ($plan->user && Auth::user()->canManage($plan->user) && $plan->status === 'submitted')
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($plan->tasks as $task)
                        <tr>
                            <td><strong>{{ $task->title }}</strong></td>
                            <td>{{ $task->description ?: 'No description' }}
                                {{-- Display manager feedback in table if task is rejected --}}
                                @if ($task->status === 'rejected' && $task->manager_feedback)
                                    <div style="font-size: 0.8rem; color: #ef4444; margin-top: 0.5rem; font-style: italic;">
                                        Feedback: {{ $task->manager_feedback }}</div>
                                @endif
                            </td>
                            <td>{{ ucfirst($task->day) }}</td>
                            <td><span
                                    class="priority-badge priority-{{ strtolower($task->priority) }}">{{ $task->priority }}</span>
                            </td>
                            <td><span
                                    class="status-badge status-{{ strtolower($task->status) }}">{{ str_replace('_', ' ', $task->status) }}</span>
                            </td>
                            {{-- Ensure $plan->user is a User model instance before calling canManage --}}
                            @if ($plan->user && Auth::user()->canManage($plan->user) && $plan->status === 'submitted')
                                <td>
                                    @if ($task->status === 'approved')
                                        <span class="status-badge status-approved">Approved</span>
                                    @elseif($task->status === 'rejected')
                                        <span class="status-badge status-rejected">Rejected</span>
                                    @else
                                        <form action="{{ route('tenant.weekly-plan-tasks.approve', $task->id) }}"
                                            method="POST" style="display: inline-block;">
                                            {{ csrf_field() }}
                                            {{ method_field('PATCH') }}
                                            <button type="submit" class="btn-task btn-approve" title="Approve Task"><i
                                                    class="fas fa-check"></i></button>
                                        </form>
                                        <button type="button" class="btn-task btn-reject" title="Reject Task"
                                            onclick="openTaskRejectModal({{ $task->id }}, '{{ addslashes($task->title) }}')"><i
                                                class="fas fa-times"></i></button>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $plan->user && Auth::user()->canManage($plan->user) && $plan->status === 'submitted' ? '6' : '5' }}"
                                class="empty-state">No tasks found for this week</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Task Rejection Modal -->
    <div id="taskRejectModal" class="modal" style="display:none;">
        <div class="modal-content">
            <h3>Reject Task</h3>
            <form id="taskRejectForm" method="POST">
                {{ csrf_field() }}
                {{ method_field('PATCH') }}
                <div style="margin-bottom: 1.5rem;">
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 600;">Task:</label>
                    <p id="taskTitle" style="font-weight: 600; color: #1f2937; margin: 0; font-size: 1.1rem;"></p>
                </div>
                <div style="margin-bottom: 2rem;">
                    <label for="task_manager_feedback">Feedback (Required):</label>
                    <textarea id="task_manager_feedback" name="manager_feedback" rows="4"
                        placeholder="Provide specific feedback to help the employee improve..." required></textarea>
                </div>
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn-modern btn-outline"
                        onclick="closeTaskRejectModal()">Cancel</button>
                    <button type="submit" class="btn-modern btn-danger-modern">Reject Task</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Plan Rejection Modal -->
    <div id="planRejectModal" class="modal"  style="display:none;">
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
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn-modern btn-outline"
                        onclick="closePlanRejectModal()">Cancel</button>
                    <button type="submit" class="btn-modern btn-danger-modern">Reject Plan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- NEW: Plan Deletion Confirmation Modal --}}
    <div id="deletePlanModal" style="display:none; class="modal">
        <div class="modal-content">
            <h3>Confirm Deletion</h3>
            <p style="margin-bottom: 1.5rem; color: #4a5568;">Are you sure you want to delete the weekly plan "<strong
                    id="deletePlanTitle"></strong>"? This action cannot be undone.</p>
            <form id="deletePlanForm" method="POST">
                @csrf
                @method('DELETE')
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn-modern btn-outline"
                        onclick="closeDeletePlanModal()">Cancel</button>
                    <button type="submit" class="btn-modern btn-danger-modern">Delete Plan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openTaskRejectModal(taskId, taskTitle) {
            document.getElementById('taskTitle').textContent = taskTitle;
            document.getElementById('taskRejectForm').action = '{{ url('tenant/weekly-plan-tasks') }}/' + taskId +
                '/reject';
            document.getElementById('task_manager_feedback').value = '';
            document.getElementById('taskRejectModal').style.display = 'flex';
        }

        function closeTaskRejectModal() {
            document.getElementById('taskRejectModal').style.display = 'none';
        }

        function openPlanRejectModal(planId) {
            document.getElementById('planRejectForm').action = '{{ url('tenant/weekly-plans') }}/' + planId + '/reject';
            document.getElementById('plan_rejection_reason').value = '';
            document.getElementById('planRejectModal').style.display = 'flex';
        }

        function closePlanRejectModal() {
            document.getElementById('planRejectModal').style.display = 'none';
        }

        // NEW: Delete Plan Modal functions
        function openDeletePlanModal(planId, planTitle) {
            document.getElementById('deletePlanTitle').textContent = planTitle;
            document.getElementById('deletePlanForm').action = '/tenant/weekly-plans/' + planId;
            document.getElementById('deletePlanModal').style.display = 'flex';
        }

        function closeDeletePlanModal() {
            document.getElementById('deletePlanModal').style.display = 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            var taskModal = document.getElementById('taskRejectModal');
            var planRejectModal = document.getElementById('planRejectModal');
            var deletePlanModal = document.getElementById('deletePlanModal'); // NEW

            if (event.target == taskModal) {
                closeTaskRejectModal();
            }
            if (event.target == planRejectModal) {
                closePlanRejectModal();
            }
            if (event.target == deletePlanModal) { // NEW
                closeDeletePlanModal();
            }
        }

        // Hide modals on initial load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('taskRejectModal').style.display = 'none';
            document.getElementById('planRejectModal').style.display = 'none';
            document.getElementById('deletePlanModal').style.display = 'none'; // NEW
        });
    </script>
@endsection
