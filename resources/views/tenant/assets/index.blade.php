@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Assets</h2>

        <div class="asset-filters">
            <div class="filters-header">
                @can('create_assets')
                    <a href="{{ route('tenant.assets.create') }}" class="new-asset-button">
                        <i class="fas fa-add"></i> New Asset
                    </a>
                @endcan


                <a href="{{ route('tenant.assets.export', request()->query()) }}" class="new-asset-button">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </a>
            </div>
        </div>

        <form action="{{ route('tenant.assets.index') }}" method="GET" id="filterForm">
            <div class="filters-main">
                {{-- Search Bar --}}
                <div class="search-wrapper">
                    <input type="text" class="search-input" placeholder="Search by name" name="search"
                        value="{{ request('search') }}" onchange="document.getElementById('filterForm').submit()">
                    @if (request()->anyFilled(['search', 'total_amount_range', 'status', 'location', 'model', 'type', 'cost']))
                        <a href="{{ route('tenant.assets.index') }}" class="clear-filters">
                            <i class="fas fa-times"></i> Clear All Filters
                        </a>
                    @endif
                </div>
            </div>

            <div class="filters-secondary">
                {{-- Status Filter --}}
                <div class="select-wrapper">
                    <select name="status" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Filter by Status</option>
                        <option value="in use" {{ request('status') == 'in use' ? 'selected' : '' }}>In Use</option>
                        <option value="not in use" {{ request('status') == 'not in use' ? 'selected' : '' }}>Not in Use
                        </option>
                        <option value="in service" {{ request('status') == 'in service' ? 'selected' : '' }}>In Service
                        </option>
                    </select>
                </div>

                {{-- Location Filter --}}
                <div class="select-wrapper">
                    <select name="location" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Filter by Location</option>
                        <option value="Cape Town" {{ request('location') == 'Cape Town' ? 'selected' : '' }}>Cape Town
                        </option>
                        <option value="Durban" {{ request('location') == 'Durban' ? 'selected' : '' }}>Durban</option>
                        <option value="East London" {{ request('location') == 'East London' ? 'selected' : '' }}>East
                            London</option>
                        <option value="Johannesburg" {{ request('location') == 'Johannesburg' ? 'selected' : '' }}>
                            Johannesburg</option>
                    </select>
                </div>

                {{-- Model Filter --}}
                <div class="select-wrapper">
                    <select name="model" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Filter by Model</option>
                        <option value="Stihl Chainsaw" {{ request('model') == 'Stihl Chainsaw' ? 'selected' : '' }}>Stihl
                            Chainsaw</option>
                        <option value="Blower Stihl" {{ request('model') == 'Blower Stihl' ? 'selected' : '' }}>Blower
                            Stihl</option>
                        <option value="BI Cutter" {{ request('model') == 'BI Cutter' ? 'selected' : '' }}>BI Cutter
                        </option>
                        <option value="Lenovo" {{ request('model') == 'Lenovo' ? 'selected' : '' }}>Lenovo</option>
                        <option value="Apple" {{ request('model') == 'Apple' ? 'selected' : '' }}>Apple</option>
                    </select>
                </div>

                {{-- Equipment Type Filter --}}
                <div class="select-wrapper">
                    <select name="name" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Filter by Type</option>
                        <option value="Office Equipment" {{ request('name') == 'Office Equipment' ? 'selected' : '' }}>
                            Office Equipment</option>
                        <option value="Equipment" {{ request('name') == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="Vehicle" {{ request('name') == 'Vehicle' ? 'selected' : '' }}>Vehicle</option>
                    </select>
                </div>

                {{-- Cost Filter --}}
                <div class="select-wrapper">
                    <select name="cost" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Filter by Cost</option>
                        <option value="0-50000" {{ request('cost') == '0-50000' ? 'selected' : '' }}>R0 - R50000</option>
                        <option value="50001-100000" {{ request('cost') == '50001-100000' ? 'selected' : '' }}>
                            R50001-R100000</option>
                        <option value="100001-150000" {{ request('cost') == '100001-150000' ? 'selected' : '' }}>
                            R100001-R150000</option>
                        <option value="150000+" {{ request('cost') == '150000+' ? 'selected' : '' }}>More than R150000
                        </option>
                    </select>
                </div>
            </div>
        </form>




        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-hover ">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Serial Number</th>
                    <th>Model</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Cost</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assets as $asset)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $asset->name }}</td>
                        <td>{{ $asset->serial_number }}</td>
                        <td>{{ $asset->model }}</td>
                        <td>{{ $asset->assetType->name ?? 'N/A' }}</td>
                        <td>{{ $asset->status }}</td>
                        <td>{{ number_format($asset->cost, 2) }}</td>
                        <td>{{ $asset->location }}</td>
                        <td>
                            @can('view_assets')
                                <a href="{{ route('tenant.assets.show', $asset) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            @endcan

                            @can('edit_assets')
                                <a href="{{ route('tenant.assets.edit', $asset) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endcan

                            @can('delete_assets')
                                <form action="{{ route('tenant.assets.destroy', $asset) }}" method="POST"
                                    style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this asset?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endcan
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="9">No assets found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $assets->links() }}
    </div>

    <style>
        .asset-filters {
            background-color: #f8f9fa;
            padding: 1.25rem;
            border-radius: 4px;
        }

        .filters-header {
            margin-bottom: 1rem;
        }

        .new-asset-button {
            display: inline-block;
            padding: 0.6rem 1.25rem;
            background-color: #4a90e2;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .new-asset-button:hover {
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
