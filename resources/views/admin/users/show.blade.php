@extends('layouts.app')

@section('content')
    <div>
        <div class="emp-profile-container">
            <div class="emp-card">
                @if (!$user)
                    <div class="emp-alert emp-alert-info">
                        <span class="emp-alert-icon"><i class="fa fa-info-circle"></i></span>
                        User profile not found.
                    </div>
                @else
                    <div class="row g-0">
                        <!-- Profile Sidebar -->
                        <div class="col-md-3 text-center p-4">
                            <div class="emp-profile-sidebar">
                                <div class="emp-profile-image-container">
                                    @if (isset($user->employee) && $user->employee->picture_path)
                                        <img src="{{ asset('storage/' . $user->employee->picture_path) }}" alt="User Photo"
                                            class="emp-profile-image">
                                    @else
                                        <div class="emp-profile-image-placeholder">
                                            <img src="{{ asset('storage/user-vecter.png') }}" alt="Default Profile"
                                                class="emp-profile-image">
                                        </div>
                                    @endif
                                </div>

                                <h2 class="emp-profile-name">{{ $user->name }}</h2>
                                <div class="emp-profile-position">{{ $user->employee->position ?? 'General' }}</div>
                                <div class="emp-profile-department">{{ $teamName ??
                                    'Department
                                Not Specified' }}</div>

                                <div class="emp-profile-quick-stats">
                                    <div class="emp-stat-item">
                                        <div class="emp-stat-value">{{ $userProjects->count() }}</div>
                                        <div class="emp-stat-label">Projects</div>
                                    </div>
                                    <div class="emp-stat-item">
                                        <div class="emp-stat-value">{{ isset($subordinates) ? $subordinates->count() : 0 }}
                                        </div>
                                        <div class="emp-stat-label">Team Members</div>
                                    </div>
                                    <div class="emp-stat-item">
                                        <div class="emp-stat-value">{{ $user->employee->completion_percentage ?? '0' }}%
                                        </div>
                                        <div class="emp-stat-label">Completion</div>
                                    </div>
                                </div>

                                <div class="emp-profile-bio">
                                    {{ $user->employee->bio ?? 'No biography provided.' }}
                                </div>

                                <div class="emp-profile-divider"></div>
                                <div class="emp-profile-contact">
                                    <div class="emp-contact-item">
                                        <span class="emp-contact-icon">📧</span> {{ $user->email }}
                                    </div>
                                    <div class="emp-contact-item">
                                        <span class="emp-contact-icon">📱</span>
                                        {{ $user->employee->phone_number ?? 'Not provided' }}
                                    </div>
                                    <div class="emp-contact-item">
                                        <span class="emp-contact-icon">📍</span>
                                        {{ $user->employee->office_location ??
                                            'Office 
                                        Not Specified' }}
                                    </div>
                                </div>

                                <div class="emp-profile-social">
                                    <a href="#" class="emp-social-link" title="LinkedIn">in</a>
                                    <a href="#" class="emp-social-link" title="Twitter">𝕏</a>
                                    <a href="#" class="emp-social-link" title="Slack">G</a>
                                </div>

                                {{--                                <div class="emp-profile-tags"> --}}
                                {{--                                    @foreach (explode(',', $user->employee->skills ?? '') as $skill) --}}
                                {{--                                        @if (!empty(trim($skill))) --}}
                                {{--                                            <span class="emp-profile-tag">{{ trim($skill) }}</span> --}}
                                {{--                                        @endif --}}
                                {{--                                    @endforeach --}}
                                {{--                                </div> --}}

                                <div class="emp-profile-divider"></div>
                                <div class="emp-profile-badges">
                                    @if (isset($user->employee->badges))
                                        @foreach ($user->employee->badges as $badge)
                                            <div class="emp-badge">
                                                <div class="emp-badge-icon" style="border-color: {{ $badge->color }};">
                                                    {{ $badge->icon }}</div>
                                                <div class="emp-badge-tooltip">{{ $badge->name }}</div>
                                            </div>
                                        @endforeach
                                    @else
                                        <!-- Default badges if none specified -->
                                        <div class="emp-badge">
                                            @if ($user->teams->isNotEmpty())
                                                <div class="emp-badge-icon" style="border-color: silver;">⭐</div>
                                                <div class="emp-badge-tooltip">Team Member</div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="col-md-9">
                            <div class="emp-stats-grid">
                                <!-- Total Projects card -->
                                <div class="emp-stat-card">
                                    <div class="emp-stat-title">Total Projects</div>
                                    <div class="emp-stat-value emp-stat-primary">{{ $userProjects->count() }}</div>
                                </div>

                                <!-- Completed Projects card -->
                                <div class="emp-stat-card">
                                    <div class="emp-stat-title">Completed Projects</div>
                                    <div class="emp-stat-value emp-stat-success">
                                        {{ $userProjects->where('status', 3)->count() }}</div>
                                </div>

                                <!-- Next deadline card -->
                                <div class="emp-stat-card">
                                    <div class="emp-stat-title">Next deadline</div>
                                    <div class="emp-stat-value emp-stat-warning">
                                        @php
                                            $nextDeadline = $userProjects
                                                ->filter(function ($project) {
                                                    return $project->endDate && $project->endDate->isFuture();
                                                })
                                                ->sortBy('endDate')
                                                ->first();
                                        @endphp
                                        {{ $nextDeadline ? $nextDeadline->endDate->format('d M Y') : 'N/A' }}
                                    </div>
                                </div>
                            </div>

                            <div class="emp-tabs" id="profileTabs" role="tablist">
                                <div class="emp-tab-item">
                                    <a class="emp-tab-link active" id="personal-tab" data-toggle="tab" href="#personal"
                                        role="tab">Personal Info</a>
                                </div>
                                <div class="emp-tab-item">
                                    <a class="emp-tab-link" id="work-tab" data-toggle="tab" href="#work"
                                        role="tab">Work Details</a>
                                </div>
                                <div class="emp-tab-item">
                                    <a class="emp-tab-link" id="team-tab" data-toggle="tab" href="#team"
                                        role="tab">Team Info</a>
                                </div>
                            </div>

                            <div class="emp-tab-content" id="profileTabsContent">
                                <!-- Personal Information Tab -->
                                <div class="emp-tab-pane active" id="personal" role="tabpanel">
                                    <div class="emp-info-row">
                                        <div class="emp-info-label">@lang('quickadmin.users.fields.name'):</div>
                                        <div class="emp-info-value">{{ $user->name }}</div>
                                    </div>
                                    <div class="emp-info-row">
                                        <div class="emp-info-label">@lang('quickadmin.users.fields.email'):</div>
                                        <div class="emp-info-value">{{ $user->email }}</div>
                                    </div>
                                    <div class="emp-info-row">
                                        <div class="emp-info-label">Phone Number:</div>
                                        <div class="emp-info-value">{{ $user->employee->phone_number ?? 'Not provided' }}
                                        </div>
                                    </div>
                                    <div class="emp-info-row">
                                        <div class="emp-info-label">Birth Day:</div>
                                        <div class="emp-info-value">
                                            {{ isset($user->employee->date_of_birth) ? $user->employee->date_of_birth->format('d F') : 'Not provided' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Work Details Tab -->
                                <div class="emp-tab-pane" id="work" role="tabpanel">
                                    <div class="emp-info-row">
                                        <div class="emp-info-label">@lang('quickadmin.users.fields.role'):</div>
                                        <div class="emp-info-value">{{ $user->role->title or '' }}</div>
                                    </div>
                                    <div class="emp-info-row">
                                        <div class="emp-info-label">Position:</div>
                                        <div class="emp-info-value">{{ $user->employee->position ??
                                            '
                                        Not Specified' }}
                                        </div>
                                    </div>
                                    <div class="emp-info-row">
                                        <div class="emp-info-label">Emergency Phone:</div>
                                        <div class="emp-info-value">
                                            {{ $user->employee->emergency_contact_phone ??
                                                '
                                            Not Specified' }}</div>
                                    </div>
                                    <div class="emp-info-row">
                                        <div class="emp-info-label">Start Date:</div>
                                        <div class="emp-info-value">
                                            {{ isset($user->employee->start_date)
                                                ? $user->employee->start_date->format('F d, Y')
                                                : '
                                            Not Specified' }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Team Details Tab -->
                                <div class="emp-tab-pane" id="team" role="tabpanel">
                                    <div class="emp-info-row">
                                        <div class="emp-info-label">@lang('quickadmin.users.fields.team'):</div>
                                        <div class="emp-info-value">
                                            @if ($user->teams->isNotEmpty())
                                                @foreach ($user->teams as $team)
                                                    <span
                                                        class="emp-profile-tag">{{ $team->name ?? 'Not Assigned' }}</span>
                                                @endforeach
                                            @else
                                                Not Assigned
                                            @endif

                                        </div>
                                    </div>
                                    <div class="emp-info-row">
                                        <div class="emp-info-label">Manager:</div>
                                        <div class="emp-info-value">{{ $user->getManager()->name ?? 'Not Assigned' }}
                                        </div>
                                    </div>
                                    <div class="emp-info-row">
                                        <div class="emp-info-label">Department:</div>
                                        <div class="emp-info-value">{{ $teamName ??
                                            '
                                        Not Specified' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <h3 class="emp-section-title">Projects</h3>
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
                                        @forelse($userProjects as $project)
                                            <tr
                                                onclick="window.location='{{ route('tenant.projects.show', $project->id) }}'">
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
                                                    <td colspan="6" class="text-center">No Projects</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <p>&nbsp;</p>
                                <a href="javascript:history.back();" class="btn btn-default">
                                    @lang('quickadmin.qa_back_to_list')
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endsection
