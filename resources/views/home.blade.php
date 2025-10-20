@extends('layouts.app')

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

.profile-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
}

.form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
    overflow: hidden;
}

.form-card-header {
    background: linear-gradient(135deg, #216592 0%, #1a5278 100%);
    color: white;
    padding: 1.5rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.form-card-header h4 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 600;
}

.form-card-body {
    padding: 2rem;
}

.alert {
    padding: 1rem 1.5rem;
    margin-bottom: 1.5rem;
    border-radius: 8px;
    border: none;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-info {
    background: linear-gradient(135deg, #cce7ff 0%, #b3d9ff 100%);
    color: #0c5aa6;
    border-left: 4px solid #007bff;
}

.profile-grid {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.profile-sidebar {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    text-align: center;
    height: fit-content;
}

.profile-image-container {
    margin-bottom: 1.5rem;
}

.profile-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #216592;
    box-shadow: 0 4px 12px rgba(33, 101, 146, 0.2);
}

.profile-name {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
    margin: 0 0 0.5rem 0;
}

.profile-position {
    font-size: 1.1rem;
    color: #216592;
    font-weight: 500;
    margin-bottom: 0.25rem;
}

.profile-department {
    color: #6c757d;
    margin-bottom: 1.5rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-item {
    text-align: center;
    padding: 1rem;
    background: #f8fafc;
    border-radius: 8px;
    border: 2px solid #e2e8f0;
}

.stat-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: #216592;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.profile-bio {
    background: #f8fafc;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    color: #4a5568;
    font-style: italic;
}

.contact-info {
    text-align: left;
    margin-bottom: 1.5rem;
}

.contact-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 0.75rem;
    color: #4a5568;
}

.social-links {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 1.5rem;
}

.social-link {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #216592;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-weight: bold;
    transition: all 0.2s ease;
}

.social-link:hover {
    background: #1a5278;
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
}

.main-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.top-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
    margin-bottom: 2rem;
}

.top-stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    border-left: 4px solid #216592;
}

.top-stat-title {
    font-size: 0.875rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.5rem;
}

.top-stat-value {
    font-size: 2rem;
    font-weight: 600;
    color: #216592;
}

.tabs-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.tab-nav {
    background: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
    display: flex;
    overflow-x: auto;
}

.tab-link {
    padding: 1rem 1.5rem;
    color: #6c757d;
    text-decoration: none;
    font-weight: 500;
    border-bottom: 3px solid transparent;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.tab-link.active {
    color: #216592;
    border-bottom-color: #216592;
    background: white;
}

.tab-link:hover {
    color: #216592;
    text-decoration: none;
}

.tab-content {
    padding: 2rem;
}

.tab-pane {
    display: none;
}

.tab-pane.active {
    display: block;
}

.info-grid {
    display: grid;
    gap: 1rem;
}

.info-row {
    display: grid;
    grid-template-columns: 200px 1fr;
    gap: 1rem;
    padding: 1rem 0;
    border-bottom: 1px solid #e2e8f0;
}

.info-row:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #2d3748;
}

.info-value {
    color: #4a5568;
}

.leave-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.leave-card {
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
}

.leave-card h5 {
    margin: 0 0 1rem 0;
    color: #2d3748;
    font-weight: 600;
}

.progress-container {
    background: #e2e8f0;
    border-radius: 8px;
    height: 24px;
    position: relative;
    margin-bottom: 0.5rem;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    font-weight: 500;
    transition: width 0.3s ease;
}

.progress-regular {
    background: linear-gradient(135deg, #216592 0%, #1a5278 100%);
}

.progress-info {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.progress-warning {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.progress-remaining {
    font-size: 0.875rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.project-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
}

.project-table thead {
    background: linear-gradient(135deg, #216592 0%, #1a5278 100%);
    color: white;
}

.project-table th,
.project-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e2e8f0;
}

.project-table tbody tr {
    cursor: pointer;
    transition: all 0.2s ease;
}

.project-table tbody tr:hover {
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-planned {
    background: #e3f2fd;
    color: #1976d2;
}

.status-ongoing {
    background: #fff3e0;
    color: #f57c00;
}

.status-completed {
    background: #e8f5e8;
    color: #388e3c;
}

.status-unknown {
    background: #f5f5f5;
    color: #757575;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
    margin: 2rem 0 1rem 0;
}

@media (max-width: 768px) {
    .plan-container {
        padding: 10px;
    }
    
    .profile-grid {
        grid-template-columns: 1fr;
    }
    
    .top-stats {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .leave-cards {
        grid-template-columns: 1fr;
    }
    
    .info-row {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .tab-nav {
        flex-direction: column;
    }
    
    .project-table {
        font-size: 0.875rem;
    }
    
    .project-table th,
    .project-table td {
        padding: 0.75rem 0.5rem;
    }
}
</style>

<div class="plan-container">
    @if(!$employee)
        <div class="plan-header">
            <h1>👤 Employee Profile</h1>
            <div class="profile-subtitle">Your personal information and work details</div>
        </div>
        
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i>
            You do not have an employee profile created yet. Please contact the HR department.
        </div>
    @else
        <!-- Header -->
        <div class="plan-header">
            <h1>👤 {{ auth()->user()->name }}</h1>
            <div class="profile-subtitle">{{ $employee->position ?? 'General' }} • {{ $team ?? 'Team Not Specified' }}</div>
        </div>

        <!-- Top Stats -->
        <div class="top-stats">
            <div class="top-stat-card">
                <div class="top-stat-title">Active Projects</div>
                <div class="top-stat-value">{{ auth()->user()->activeProjects->count() }}</div>
            </div>
            <div class="top-stat-card">
                <div class="top-stat-title">Completed Projects</div>
                <div class="top-stat-value">{{ auth()->user()->completedProjects->count() }}</div>
            </div>
            <div class="top-stat-card">
                <div class="top-stat-title">Next Deadline</div>
                <div class="top-stat-value">{{ $formattedDeadline }}</div>
            </div>
        </div>

        <!-- Profile Grid -->
        <div class="profile-grid">
            <!-- Profile Sidebar -->
            <div class="profile-sidebar">
                <div class="profile-image-container">
                    @if($employee->picture_path)
                        <img src="{{ asset('storage/' . $employee->picture_path) }}" alt="Employee Photo" class="profile-image">
                    @else
                        <img src="{{ asset('storage/user-vecter.png') }}" alt="Default Profile" class="profile-image">
                    @endif
                </div>
                
                <h2 class="profile-name">{{ auth()->user()->name }}</h2>
                <div class="profile-position">{{ $employee->position ?? 'General' }}</div>
                <div class="profile-department">{{ $team ?? 'Team Not Specified' }}</div>
                
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">{{ $currentEmployeeProjects->count() }}</div>
                        <div class="stat-label">Projects</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ $subordinates->count() }}</div>
                        <div class="stat-label">Team Members</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">-</div>
                        <div class="stat-label">Completion</div>
                    </div>
                </div>
                
                <div class="profile-bio">{{ $employee->bio ?? "No Bio" }}</div>
                
                <div class="contact-info">
                    <div class="contact-item">
                        <span>📧</span> {{ $employee->email }}
                    </div>
                    <div class="contact-item">
                        <span>📱</span> {{ $employee->phone_number ?? 'Not provided' }}
                    </div>
                    <div class="contact-item">
                        <span>📍</span> Somerset West Office
                    </div>
                </div>
                
                <div class="social-links">
                    <a href="https://www.linkedin.com/company/contourenvirogroup" target="_blank" class="social-link" title="LinkedIn">in</a>
                    <a href="https://www.facebook.com/ContourEnviroGroup/" target="_blank" class="social-link" title="Facebook">f</a>
                    <a href="#" class="social-link" title="Google">G</a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <!-- Tabs -->
                <div class="tab-nav" id="profileTabs" role="tablist">
                    <a class="tab-link active" id="personal-tab" data-toggle="tab" href="#personal" role="tab">Personal Info</a>
                    <a class="tab-link" id="work-tab" data-toggle="tab" href="#work" role="tab">Work Details</a>
                    <a class="tab-link" id="leave-tab" data-toggle="tab" href="#leave" role="tab">Leave & Attendance</a>
                    <a class="tab-link" id="financial-tab" data-toggle="tab" href="#financial" role="tab">Financial</a>
                </div>

                <div class="tab-content" id="profileTabsContent">
                    <!-- Personal Information Tab -->
                    <div class="tab-pane active" id="personal" role="tabpanel">
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-label">Name:</div>
                                <div class="info-value">{{ $employee->first_name }} {{ $employee->last_name }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Date of Birth:</div>
                                <div class="info-value">{{ $employee->date_of_birth ? $employee->date_of_birth->format('F d, Y') : 'Not provided' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">ID Number:</div>
                                <div class="info-value">{{ $employee->id_number ?? 'Not provided' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Address:</div>
                                <div class="info-value">
                                    @if($employee->street_address || $employee->suburb || $employee->city || $employee->postal_code)
                                        {{ $employee->street_address }}, {{ $employee->suburb }}, {{ $employee->city }}, {{ $employee->postal_code }}
                                    @else
                                        Not provided
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Work Details Tab -->
                    <div class="tab-pane" id="work" role="tabpanel">
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-label">Position:</div>
                                <div class="info-value">{{ $employee->position ?? 'General' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Email:</div>
                                <div class="info-value">{{ $employee->email }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Employee Type:</div>
                                <div class="info-value">{{ ucfirst($employee->employee_type) }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Start Date:</div>
                                <div class="info-value">{{ $employee->start_date ? $employee->start_date->format('F d, Y') : 'Not specified' }}</div>
                            </div>
                            @if($employee->end_date)
                            <div class="info-row">
                                <div class="info-label">End Date:</div>
                                <div class="info-value">{{ $employee->end_date->format('F d, Y') }}</div>
                            </div>
                            @endif
                            @if($employee->overtime_rate > 0)
                            <div class="info-row">
                                <div class="info-label">Overtime Rate:</div>
                                <div class="info-value">{{ number_format($employee->overtime_rate, 2) }}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Leave & Attendance Tab -->
                    <div class="tab-pane" id="leave" role="tabpanel">
                        <div class="leave-cards">
                            <!-- Annual Leave Card -->
                            <div class="leave-card">
                                <h5>Annual Leave</h5>
                                <div class="progress-container">
                                    @php
                                        $annualLeavePercentage = $employee->annual_leave_entitlement > 0 
                                            ? ($employee->annual_leave_taken / $employee->annual_leave_entitlement) * 100 
                                            : 0;
                                        $remainingAnnualLeave = $employee->annual_leave_entitlement - $employee->annual_leave_taken;
                                    @endphp
                                    <div class="progress-bar progress-regular" style="width: {{ $annualLeavePercentage }}%">
                                        <span>{{ $employee->annual_leave_taken ?? 0 }} / {{ $employee->annual_leave_entitlement ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="progress-remaining">{{ $remainingAnnualLeave }} days remaining</div>
                                <div class="info-row">
                                    <div class="info-label">Current Balance:</div>
                                    <div class="info-value">{{ $employee->annual_leave_balance ?? 0 }} days</div>
                                </div>
                            </div>

                            <!-- Sick Leave Card -->
                            <div class="leave-card">
                                <h5>Sick Leave</h5>
                                <div class="progress-container">
                                    @php
                                        $sickLeavePercentage = ($employee->sick_days_allowed > 0) 
                                            ? ($employee->sick_days_taken / $employee->sick_days_allowed) * 100 
                                            : 0;
                                        $remainingSickLeave = $employee->sick_days_allowed - $employee->sick_days_taken;
                                    @endphp
                                    <div class="progress-bar progress-info" style="width: {{ $sickLeavePercentage }}%">
                                        <span>{{ $employee->sick_days_taken ?? 0 }} / {{ $employee->sick_days_allowed ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="progress-remaining">{{ $remainingSickLeave }} days remaining</div>
                                <div class="info-row">
                                    <div class="info-label">Current Balance:</div>
                                    <div class="info-value">{{ $employee->sick_leave_balance ?? 0 }} days</div>
                                </div>
                            </div>

                            <!-- Compassionate Leave Card -->
                            <div class="leave-card">
                                <h5>Compassionate Leave</h5>
                                <div class="progress-container">
                                    @php
                                        $compassionateLeavePercentage = $employee->compassionate_days_allowed > 0 
                                            ? ($employee->compassionate_days_taken / $employee->compassionate_days_allowed) * 100 
                                            : 0;
                                        $remainingCompassionateLeave = $employee->compassionate_days_allowed - $employee->compassionate_days_taken;
                                    @endphp
                                    <div class="progress-bar progress-warning" style="width: {{ $compassionateLeavePercentage }}%">
                                        <span>{{ $employee->compassionate_days_taken ?? 0 }} / {{ $employee->compassionate_days_allowed ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="progress-remaining">{{ $remainingCompassionateLeave }} days remaining</div>
                                <div class="info-row">
                                    <div class="info-label">Current Balance:</div>
                                    <div class="info-value">{{ $employee->compassionate_leave_balance ?? 0 }} days</div>
                                </div>
                            </div>

                            <!-- Unpaid Leave Card -->
                            <div class="leave-card">
                                <h5>Unpaid Leave</h5>
                                <div class="info-row">
                                    <div class="info-label">Taken This Year:</div>
                                    <div class="info-value">{{ $employee->unpaid_leave_taken ?? 0 }} days</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Financial Tab -->
                    <div class="tab-pane" id="financial" role="tabpanel">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i>
                            Financial information is only visible to you and the finance department.
                        </div>
                        <div class="info-grid">
                            <div class="info-row">
                                <div class="info-label">Bank Name:</div>
                                <div class="info-value">{{ $employee->bank_name ?? 'Not provided' }}</div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Account Number:</div>
                                <div class="info-value">
                                    {{-- @if($employee->bank_account_number)
                                        {{ substr_replace($employee->bank_account_number, str_repeat('*', strlen($employee->bank_account_number) - 4), 0, strlen($employee->bank_account_number) - 4) }}
                                    @else
                                        Not provided
                                    @endif --}}
                                </div>
                            </div>
                            <div class="info-row">
                                <div class="info-label">Tax Number:</div>
                                <div class="info-value">{{ $employee->tax_number ?? 'Not provided' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects Section -->
        <div class="form-card">
            <div class="form-card-header">
                <h4>📋 My Projects</h4>
            </div>
            <div class="form-card-body">
                <div class="table-responsive">
                    <table class="project-table">
                        <thead>
                            <tr>
                                <th>Project Name</th>
                                <th>Location</th>
                                <th>Start Date</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Completion %</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($currentEmployeeProjects as $project)
                            <tr onclick="window.location='{{ route('tenant.projects.show', $project->id) }}'">
                                <td>{{ $project->project_name }}</td>
                                <td>{{ $project->location->name ?? 'N/A' }}</td>
                                <td>{{ $project->startDate->format('d M Y') ?? 'N/A' }}</td>
                                <td>{{ $project->endDate->format('d M Y') ?? 'N/A' }}</td>
                                <td>
                                    @switch($project->status)
                                        @case(1)
                                            <span class="status-badge status-planned">Planned</span>
                                            @break
                                        @case(2)
                                            <span class="status-badge status-ongoing">Ongoing</span>
                                            @break
                                        @case(3)
                                            <span class="status-badge status-completed">Completed</span>
                                            @break
                                        @default
                                            <span class="status-badge status-unknown">Unknown</span>
                                    @endswitch
                                </td>
                                <td>{{ $project->completion_percentage ?? 0 }}%</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">No personal projects found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if(auth()->user()->isManager())
        <!-- Subordinates Section -->
        <div class="form-card">
            <div class="form-card-header">
                <h4>👥 My Subordinates</h4>
            </div>
            <div class="form-card-body">
                <div class="table-responsive">
                    <table class="project-table">
                        <thead>
                            <tr>
                                <th>Subordinate Name</th>
                                <th>Ongoing Projects</th>
                                <th>Next Deadline</th>
                                <th>Active Projects</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subordinates as $subordinate)
                                @php
                                    $subordinateProjects = $subordinatesProjects->get($subordinate->id, collect());
                                    $ongoingCount = $subordinateProjects->where('status', 2)->count();
                                    $futureDeadlines = $subordinateProjects->filter(function($project) {
                                        return $project->endDate && $project->endDate->isFuture();
                                    });
                                    $nextDeadline = $futureDeadlines->isNotEmpty() ? $futureDeadlines->min('endDate') : null;
                                    $activeCount = $subordinateProjects->count();
                                @endphp
                                <tr onclick="window.location='{{ route('admin.users.show', $subordinate->id) }}'">
                                    <td>{{ $subordinate->name }}</td>
                                    <td>{{ $ongoingCount }}</td>
                                    <td>
                                        @if($nextDeadline)
                                            {{ $nextDeadline->format('d M Y') }}
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    <td>{{ $activeCount }}</td>
                                </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No subordinates found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Smooth tab switching
    const tabLinks = document.querySelectorAll('.tab-link');
    const tabPanes = document.querySelectorAll('.tab-pane');

    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetTab = this.getAttribute('href').substring(1);

            // Remove active classes
            tabLinks.forEach(l => l.classList.remove('active'));
            tabPanes.forEach(p => p.classList.remove('active'));

            // Add active classes
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });

    // Project row click animation
    const projectRows = document.querySelectorAll('.project-table tbody tr');
    projectRows.forEach(row => {
        row.addEventListener('click', function() {
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = 'scale(1)';
            }, 150);
        });
    });
});
</script>
@endpush