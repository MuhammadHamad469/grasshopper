@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-4">Projects</h2>

        <div>
            {{--      <a href="{{ route('tenant.projects.create') }}" class="btn btn-primary col-md-2 mb-3">New SMME</a> --}}
            <div class="project-filters">
                <div class="filters-header">
                    
                    @can('create_projects')
                        <a href="{{ route('tenant.projects.create') }}" class="btn new-project-button">
                            <i class="fas fa-add"></i> New Project
                        </a>
                    @endcan

                    <a href="{{ route('tenant.projects.export', request()->query()) }}" class="btn new-project-button">
                        <i class="fas fa-file-excel mr-1"></i> Export to Excel
                    </a>
                </div>


                <!--Search and Filter Section -->
                <div class="project-filters">
                    <form action="{{ route('tenant.projects.index') }}" method="GET" id="searchForm">
                        <div class="filters-main">
                            {{-- Search Input with Button --}}
                            <div class="search-wrapper">
                                <input type="text" class="search-input" placeholder="Search projects name" name="search"
                                    value="{{ request('search') }}">
                                <button type="submit" class="filter-apply">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                            <div class="filters-secondary">
                                {{-- Project Type Filter --}}
                                <div class="select-wrapper">
                                    <select name="type" class="filter-select"
                                        onchange="document.getElementById('searchForm').submit()">
                                        <option value="">All Types</option>
                                        @foreach ($projectTypes as $type)
                                            <option value="{{ $type->id }}"
                                                {{ request('type') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Status Filter --}}
                                <div class="select-wrapper">
                                    <select name="status" class="filter-select"
                                        onchange="document.getElementById('searchForm').submit()">
                                        <option value="">All Statuses</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                                            Planned
                                        </option>
                                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>
                                            Ongoing
                                        </option>
                                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>
                                            Complete
                                        </option>
                                    </select>
                                </div>

                                {{-- Team Leader Filter --}}
                                <div class="select-wrapper">
                                    <select name="team_leader" class="filter-select"
                                        onchange="document.getElementById('searchForm').submit()">
                                        <option value="">All Team Leaders</option>
                                        @foreach ($teamLeaders as $leader)
                                            <option value="{{ $leader->id }}"
                                                {{ request('team_leader') == $leader->id ? 'selected' : '' }}>
                                                {{ $leader->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Clear Filters Link --}}
                                @if (request()->anyFilled(['search', 'type', 'status', 'team_leader']))
                                    <a href="{{ route('tenant.projects.index') }}" class="clear-filters">
                                        <i class="fas fa-times"></i> Clear Filters
                                    </a>
                                @endif
                            </div>
                    </form>

                </div>





                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div>
                    <div>
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Project Name</th>
                                        <th>Type</th>
                                        <th>Team Leader</th>
                                        <th>Expenses</th>
                                        <th>Budget</th>
                                        <th>Duration</th>
                                        <th>Status</th>
                                        <th style="width: 120px;">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($projects as $project)
                                        <tr>
                                            <td class="align-middle">{{ $project->project_name ?? 'Not Specified' }}</td>
                                            <td class="align-middle">{{ $project->projectType->name ?? 'Not Assigned' }}
                                            </td>
                                            <td class="align-middle">{{ $project->TeamLeader->name ?? 'Not Assigned' }}
                                            </td>
                                            <td class="align-middle">R{{ $project->total_expense ?? '0' }}</td>
                                            <td class="align-middle">R{{ $project->budget ?? '0' }}</td>
                                            <td class="align-middle">
                                                <small class="text-muted">
                                                    {{ $project->startDate->format('Y-m-d') }} -
                                                    {{ $project->endDate->format('Y-m-d') }}
                                                </small>
                                            </td>
                                            <td class="align-middle">
                                                <span
                                                    class="badge {{ $project->status == 1 ? 'badge-primary' : ($project->status == 2 ? 'badge-warning' : 'badge-success') }}">
                                                    {{ $project->status == 1 ? 'Planned' : ($project->status == 2 ? 'Ongoing' : 'Complete') }}
                                                </span>
                                            </td>
                                            <td class="align-middle">
                                                <div class="d-flex justify-content-between align-items-center">

                                                    {{-- View --}}
                                                    @can('view_projects')
                                                        @include('partials.icon_button', [
                                                            'href' => route('tenant.projects.show', $project->id),
                                                            'type' => 'info',
                                                            'icon' => 'fa-eye',
                                                            'slot' => '',
                                                            'class' => 'btn-sm',
                                                            'title' => 'View Project',
                                                        ])
                                                    @endcan

                                                    {{-- Edit --}}
                                                    @can('edit_projects')
                                                        @include('partials.icon_button', [
                                                            'href' => route('tenant.projects.edit', $project->id),
                                                            'type' => 'primary',
                                                            'icon' => 'fa-edit',
                                                            'slot' => '',
                                                            'class' => 'btn-sm',
                                                            'title' => 'Edit Project',
                                                        ])
                                                    @endcan

                                                    {{-- Delete --}}
                                                    @can('delete_projects')
                                                        <form action="{{ route('tenant.projects.destroy', $project->id) }}"
                                                            method="POST" style="display: inline-block;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger"
                                                                title="Delete Project"
                                                                onclick="return confirm('Are you sure you want to delete this project?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            </td>

                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4">
                                                <div class="text-muted">No projects found</div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    {{ $projects->appends(request()->query())->links() }}
                </div>
            </div>
        </div>

        <style>
            .table th,
            .table td {
                padding: 1rem;
            }

            .table td:last-child {
                padding-right: 0.5rem;
            }

            .btn-sm {
                padding: .25rem .5rem;
                font-size: .875rem;
                line-height: 1.5;
            }

            .d-flex>.btn-sm:not(:last-child) {
                margin-right: 0.25rem;
            }

            .filters-header {
                margin-bottom: 1rem;
            }

            .project-filters {
                background-color: #f8f9fa;
                padding: 1.25rem;
                border-radius: 4px;
            }

            .filters-secondary {
                display: flex;
                gap: 1rem;
                flex-wrap: wrap;
            }

            .filters-main {
                margin-bottom: 1rem;
            }

            .filters-wrapper {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                gap: 1rem;
            }

            .search-container {
                position: relative;
                min-width: 300px;
                display: flex;
            }

            .new-project-button {
                display: inline-block;
                padding: 0.6rem 1.25rem;
                background-color: #4a90e2;
                color: white;
                border-radius: 4px;
                text-decoration: none;
                font-size: 0.9rem;
                transition: background-color 0.2s;
            }

            .new-project-button:hover {
                background-color: #357abd;
                color: white;
            }


            .search-wrapper {
                display: flex;
                align-items: center;
                gap: 0.75rem;
            }

            .search-input {
                flex: 1;
                height: 40px;
                padding: 0.5rem 1rem;
                border: 1px solid #dee2e6;
                border-radius: 4px;
                font-size: 1.2rem;
                min-width: 200px;
            }

            .search-input:focus {
                outline: none;
                border-color: #4a90e2;
            }

            .search-button {
                height: 40px;
                padding: 0 1rem;
                background: none;
                border: none;
                color: #6c757d;
                cursor: pointer;
            }

            .search-button:hover {
                color: #4a90e2;
            }

            .select-container {
                min-width: 200px;
            }

            .filter-select {
                width: 100%;
                height: 40px;
                padding: 0.5rem 2rem 0.5rem 1rem;
                border: 1px solid #dee2e6;
                border-radius: 4px;
                font-size: 1.2rem;
                background-color: white;
                appearance: none;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 0.75rem center;
                background-size: 16px 12px;
                cursor: pointer;
            }

            .filter-select:focus {
                outline: none;
                border-color: #4a90e2;
            }

            .clear-filters {
                color: #4a90e2;
                text-decoration: none;
                font-size: 1.2rem;
                display: inline-flex;
                align-items: center;
                gap: 0.25rem;
            }

            .filter-apply {
                height: 40px;
                width: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                background: white;
                border: 1px solid #dee2e6;
                border-radius: 4px;
                color: #6c757d;
                cursor: pointer;
            }

            .filter-apply:hover {
                border-color: #4a90e2;
                color: #4a90e2;
            }

            .select-wrapper {
                flex: 1;
                min-width: 200px;
            }


            .clear-filters:hover {
                text-decoration: underline;
            }

            .create-project {
                margin-top: 1.5rem;
            }

            .create-button {
                display: inline-flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1.5rem;
                font-size: 1rem;
                color: white;
                background-color: #0d6efd;
                border: none;
                border-radius: 4px;
                text-decoration: none;
                cursor: pointer;
                transition: background-color 0.2s;
            }

            .create-button:hover {
                background-color: #0b5ed7;
            }

            @media (max-width: 768px) {
                .search-wrapper {
                    flex-wrap: wrap;
                }

                .select-wrapper,
                .experience-wrapper {
                    width: 100%;
                    flex: none;
                }
            }
        </style>
    @endsection
