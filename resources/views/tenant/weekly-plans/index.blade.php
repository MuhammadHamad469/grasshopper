@extends('layouts.app')
@section('title', 'My Weekly Plans')
@section('content')
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .plans-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .page-header h1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        .page-header p {
            color: #6b7280;
            font-size: 1.1rem;
            margin: 0.5rem 0 0 0;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .plans-grid {
            display: grid;
            gap: 1.5rem;
        }

        .plan-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .plan-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-draft {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            color: #374151;
        }

        .status-submitted {
            background: linear-gradient(135deg, #dbeafe, #bfdbfe);
            color: #1e40af;
        }

        .status-approved {
            background: linear-gradient(135deg, #d1fae5, #a7f3d0);
            color: #065f46;
        }

        .status-rejected {
            background: linear-gradient(135deg, #fee2e2, #fecaca);
            color: #991b1b;
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
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }

        .btn-outline {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(0, 0, 0, 0.1);
            color: #374151;
        }

        .feedback-alert {
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 1px solid #f59e0b;
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1rem;
            color: #92400e;
        }

        .alert {
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1rem;
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

        /* Modal Specific Styles (copied from show.blade.php for consistency) */
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
    </style>
    <div class="plans-container">
        <!-- Page Header -->
        <div class="page-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="margin: 0; font-size: 2rem;">📅 My Weekly Plans</h1>
                    <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Plan and track your weekly goals</p>
                </div>
                <a href="{{ route('tenant.weekly-plans.create') }}" class="btn btn-success"
                    style="background: white; color: #216592;"><i class="fas fa-plus"></i> Create New Plan</a>
            </div>
        </div>
        <div class="stats-grid">
            <div class="stat-card">
                <div style="font-size: 0.875rem; color: #6c757d; margin-bottom: 0.5rem;">TOTAL PLANS</div>
                <div style="font-size: 1.5rem; font-weight: 600;">{{ $plans->count() }}</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 0.875rem; color: #6c757d; margin-bottom: 0.5rem;">PENDING APPROVAL</div>
                <div style="font-size: 1.5rem; font-weight: 600; color: #f59e0b;">
                    {{ $plans->where('status', 'submitted')->count() }}</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 0.875rem; color: #6c757d; margin-bottom: 0.5rem;">APPROVED</div>
                <div style="font-size: 1.5rem; font-weight: 600; color: #10b981;">
                    {{ $plans->where('status', 'approved')->count() }}</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 0.875rem; color: #6c757d; margin-bottom: 0.5rem;">DRAFTS</div>
                <div style="font-size: 1.5rem; font-weight: 600; color: #6b7280;">
                    {{ $plans->where('status', 'draft')->count() }}</div>
            </div>
            <div class="stat-card">
                <div style="font-size: 0.875rem; color: #6c757d; margin-bottom: 0.5rem;">Rejected</div>
                <div style="font-size: 1.5rem; font-weight: 600; color: #6b7280;">
                    {{ $plans->where('status', 'rejected')->count() }}</div>
            </div>
        </div>
        {{-- @if (Auth::user()->isManager())
            <!-- Stats -->
        
        @endif --}}

        <!-- Plans List -->
        <div class="plans-grid">
            @forelse($plans as $plan)
                <div class="plan-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 600; color: #1a202c; margin: 0;">
                                Week of {{ $plan->start_date ? $plan->start_date->format('M d, Y') : 'Date not set' }}
                            </h3>
                            <div style="color: #6c757d; font-size: 0.875rem; margin-top: 0.25rem;">
                                @if ($plan->start_date && $plan->end_date)
                                    {{ $plan->start_date->format('M d') }} - {{ $plan->end_date->format('M d, Y') }}
                                @else
                                    Date not set
                                @endif
                            </div>
                            <div style="color: #6c757d; font-size: 0.8rem; margin-top: 0.25rem;">
                                Submitted by {{ @$plan->user->name ?? '' }}
                            </div>
                        </div>
                        <span class="status-badge status-{{ strtolower($plan->status) }}">{{ $plan->status }}</span>
                    </div>

                    @if ($plan->summary)
                        <div style="color: #4a5568; margin-bottom: 1rem; line-height: 1.5;">
                            {{ str_limit($plan->summary, 120) }}
                        </div>
                    @endif

                    <div style="display: flex; gap: 1rem; margin-bottom: 1rem; font-size: 0.875rem; color: #6c757d;">
                        <span><i class="fas fa-tasks"></i> {{ $plan->tasks->count() }} tasks</span>
                        <span><i class="fas fa-calendar"></i> {{ $plan->created_at->format('M d, Y') }}</span>
                        @if ($plan->status == 'submitted')
                            <span><i class="fas fa-clock"></i> Awaiting approval</span>
                        @endif
                    </div>

                    @if ($plan->status == 'rejected' && $plan->rejection_reason)
                        <div class="feedback-alert">
                            <strong>Manager Feedback:</strong> {{ $plan->rejection_reason }}
                        </div>
                    @endif

                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                        <a href="{{ route('tenant.weekly-plans.show', $plan->id) }}" class="btn btn-outline">
                            <i class="fas fa-eye"></i> View
                        </a>

                        @if ($plan->status == 'draft')
                            <a href="{{ route('tenant.weekly-plans.edit', $plan->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            @if ($plan->tasks->count() > 0)
                                <form action="{{ route('tenant.weekly-plans.submit', $plan->id) }}" method="POST"
                                    style="display: inline;">
                                    {{ csrf_field() }}
                                    {{ method_field('PATCH') }}
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-paper-plane"></i> Submit
                                    </button>
                                </form>
                            @endif
                        @elseif($plan->status == 'rejected')
                            <a href="{{ route('tenant.weekly-plans.edit', $plan->id) }}" class="btn btn-primary">
                                <i class="fas fa-edit"></i> Revise & Resubmit
                            </a>
                        @endif

                        @php
                            $canDeletePlan = $plan->user_id === auth()->id() || Auth::user()->isManager();
                        @endphp
                        @if ($canDeletePlan)
                            <button type="button" class="btn btn-danger"
                                onclick="openDeletePlanModal({{ $plan->id }}, '{{ addslashes($plan->title) }}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 3rem; color: #6c757d;">
                    <i class="fas fa-calendar-plus" style="font-size: 3rem; margin-bottom: 1rem; color: #d1d5db;"></i>
                    <h3>No weekly plans yet</h3>
                    <p>Create your first weekly plan to get started with goal tracking.</p>
                    <a href="{{ route('tenant.weekly-plans.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i>
                        Create Your First Plan</a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Plan Deletion Confirmation Modal --}}
    <div id="deletePlanModal" class="modal" style="display: none;">
        <div class="modal-content">
            <h3>Confirm Deletion</h3>
            <p style="margin-bottom: 1.5rem; color: #4a5568;">Are you sure you want to delete the weekly plan "<strong
                    id="deletePlanTitle"></strong>"? This action cannot be undone.</p>
            <form id="deletePlanForm" method="POST">
                @csrf
                @method('DELETE')
                <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                    <button type="button" class="btn btn-outline" onclick="closeDeletePlanModal()">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Plan</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Delete Plan Modal functions
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
            var deletePlanModal = document.getElementById('deletePlanModal');
            if (event.target == deletePlanModal) {
                closeDeletePlanModal();
            }
        }

        // Hide modals on initial load
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('deletePlanModal').style.display = 'none';
        });
    </script>
@endsection
