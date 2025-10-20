@extends('layouts.app')
@section('content')
    <div class="container">
        <h2>Suppliers</h2>
        <div>
{{--      <a href="{{ route('tenant.smmes.create') }}" class="btn btn-primary col-md-2 mb-3">New SMME</a>--}}
            <div class="smme-filters">
                <div class="filters-header">
                    <a href="{{ route('tenant.smmes.create') }}" class="new-smme-button">
                        <i class="fas fa-add"></i> New SMME
                    </a>
                    <a href="{{ route('tenant.tenant.smmes.export', request()->query()) }}" class="new-smme-button">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                </div>

                <form action="{{ route('tenant.smmes.index') }}" method="GET" id="filterForm">
                    <div class="filters-main">
                        {{-- Search Bar --}}
                        <div class="search-wrapper">
                            <input
                                    type="text"
                                    class="search-input"
                                    placeholder="Search by name"
                                    name="search"
                                    value="{{ request('search') }}"
                            >
                            <button type="submit" class="search-button">
                                <i class="fas fa-search"></i>
                            </button>
                            @if(request()->anyFilled(['search', 'grade', 'status', 'documents', 'experience_min', 'experience_max']))
                                <a href="{{ route('tenant.smmes.index') }}" class="clear-filters">
                                    <i class="fas fa-times"></i> Clear All Filters
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="filters-secondary">
                        {{-- Grade Filter --}}
                        <div class="select-wrapper">
                            <select name="grade" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Filter by Grade</option>
                                @foreach(['A','B','C','D'] as $grade)
                                    <option value="{{ $grade }}" {{ request('grade') == $grade ? 'selected' : '' }}>
                                        Grade {{ $grade }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Filter --}}
                        <div class="select-wrapper">
                            <select name="status" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Filter by Status</option>
                                @foreach(['red', 'yellow', 'green'] as $status)
                                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Documents Filter --}}
                        <div class="select-wrapper">
                            <select name="documents" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                                <option value="">Filter by Documents</option>
                                <option value="1" {{ request('documents') === '1' ? 'selected' : '' }}>Verified</option>
                                <option value="0" {{ request('documents') === '0' ? 'selected' : '' }}>Not Verified</option>
                            </select>
                        </div>

                        {{-- Experience Range --}}
                        <div class="experience-wrapper">
                            <input
                                    type="number"
                                    name="experience_min"
                                    class="year-input"
                                    placeholder="Min Years"
                                    value="{{ request('experience_min') }}"
                            >
                            <input
                                    type="number"
                                    name="experience_max"
                                    class="year-input"
                                    placeholder="Max Years"
                                    value="{{ request('experience_max') }}"
                            >
                            <button type="submit" class="filter-apply">
                                <i class="fas fa-filter"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="smme-filters">
        <div class="table-responsive">
        <table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>
                    <a href="{{ route('tenant.smmes.index', array_merge(request()->query(), [
                        'sort' => 'name',
                        'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'
                    ])) }}" class="text-decoration-none text-dark">
                        Business Name
                        @if(request('sort') === 'name')
                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
                <th>Registration Number</th>
                <th>
                    <a href="{{ route('tenant.smmes.index', array_merge(request()->query(), [
                        'sort' => 'years_of_experience',
                        'direction' => request('sort') === 'years_of_experience' && request('direction') === 'asc' ? 'desc' : 'asc'
                    ])) }}" class="text-decoration-none text-dark">
                        Experience
                        @if(request('sort') === 'years_of_experience')
                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
                <th>Documents</th>
                <th>
                    <a href="{{ route('tenant.smmes.index', array_merge(request()->query(), [
                        'sort' => 'grade',
                        'direction' => request('sort') === 'grade' && request('direction') === 'asc' ? 'desc' : 'asc'
                    ])) }}" class="text-decoration-none text-dark">
                        Grade
                        @if(request('sort') === 'grade')
                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
                <th>
                    <a href="{{ route('tenant.smmes.index', array_merge(request()->query(), [
                        'sort' => 'status',
                        'direction' => request('sort') === 'status' && request('direction') === 'asc' ? 'desc' : 'asc'
                    ])) }}" class="text-decoration-none text-dark">
                        Status
                        @if(request('sort') === 'status')
                            <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                        @else
                            <i class="fas fa-sort"></i>
                        @endif
                    </a>
                </th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($smmes as $smme)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $smme->name }}</td>
                    <td>{{ $smme->registration_number }}</td>
                    <td>{{ $smme->years_of_experience }} years</td>
                    <td class="@if ($smme->documents_verified == 1) text-success @else text-danger @endif">
                        {{ $smme->documents_verified ? 'Verified' : 'Not Verified' }}
                    </td>
                    <td>{{ $smme->grade }}</td>
                    <td class="
                        @if ($smme->status == 'red') text-danger
                        @elseif ($smme->status == 'yellow') text-warning
                        @elseif ($smme->status == 'green') text-success
                        @endif
                        ">
                        {{ ucfirst($smme->status) }}
                    </td>
                    <td>
                        <a href="{{ route('tenant.smmes.show', $smme) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('tenant.smmes.edit', $smme) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('tenant.smmes.destroy', $smme) }}" method="POST"
                              style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $smmes->appends(request()->query())->links() }}
    </div>

    <style>
        .smme-filters {
            background-color: #f8f9fa;
            padding: 1.25rem;
            border-radius: 4px;
        }

        .filters-header {
            margin-bottom: 1rem;
        }

        .new-smme-button {
            display: inline-block;
            padding: 0.6rem 1.25rem;
            background-color: #4a90e2;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .new-smme-button:hover {
            background-color: #357abd;
            color: white;
        }

        .filters-main {
            margin-bottom: 1rem;
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

        .clear-filters {
            color: #4a90e2;
            text-decoration: none;
            font-size: 1.2rem;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .clear-filters:hover {
            text-decoration: underline;
        }

        .filters-secondary {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .select-wrapper {
            flex: 1;
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

        .experience-wrapper {
            display: flex;
            gap: 0.5rem;
            flex: 2;
            min-width: 300px;
        }

        .year-input {
            flex: 1;
            height: 40px;
            padding: 0.5rem 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 1.2rem;
        }

        .year-input:focus {
            outline: none;
            border-color: #4a90e2;
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