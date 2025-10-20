@inject('request', 'Illuminate\Http\Request')
@extends('layouts.app')
<style>
    /*.asset-filters {*/
    /*    background-color: #f8f9fa;*/
    /*    padding: 1.25rem;*/
    /*    border-radius: 4px;*/
    /*}*/

    /*.filters-header {*/
    /*    margin-bottom: 1rem;*/
    /*}*/

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
        border-color: #2a89a6;
    }

    .clear-filters {
        background-color: #086177;
        color: rgba(255, 255, 255, 0.81);
        text-decoration: none;
        font-size: 1.2rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        border: 3px solid #086177;
        border-radius: 10px;
        padding: 4px;
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

/* End of filters...............................*/

/* Container styling */
.container {
max-width: 800px;
margin: 0 auto;
padding: 2rem;
}


/* Timeline styling */
.log-entry {
position: relative;
padding-left: 60px;
padding-bottom: 2rem;
}

/* Vertical line */
.log-entry::before {
content: '';
position: absolute;
left: 15px;
top: 0;
bottom: 0;
width: 2px;
background-color: #7bc2ec;
}

/* Timeline dot */
.log-entry .flex::before {
content: '';
position: absolute;
left: 10px;
top: 5px;
width: 12px;
height: 12px;
border-radius: 50%;
background-color: #086177;
border: 2px solid rgb(255, 255, 255);
z-index: 1;
}

/* Date/time styling */
.text-sm {
font-size: 0.875rem;
color: #6b7280;
font-weight: 500;
}

/* Title styling */
.text-lg {
font-size: 2rem;
font-weight: 600;
color: #022332;
margin-bottom: 0.25rem;
}

/* Description styling */
.text-gray-600 {
color: #4b5563;
font-size: 1.5rem;
margin-bottom: 0.25rem;
}

/* User and link styling */
.text-gray-500 {
color: #6b7280;
font-size: 1.2rem;
}

.text-gray-600-sm {
    color: #6c6969;
    font-size: 1.2rem;
}

.text-blue-500 {
color: #3b82f6;
text-decoration: none;
}

.text-blue-500:hover {
text-decoration: underline;
}

/* View Details link */
a[href="#"].text-blue-500 {
margin-left: 0.25rem;
font-size: 0.875rem;
}

/* Card styling */
.border-b {
border: 1px solid #f3f4f6;
border-radius: 8px;
background-color: #ffffff;
padding: 1rem;
margin-bottom: 1rem;
}

/* Layout adjustments */
.flex {
display: flex;
gap: 1rem;
border-radius: 4px;
padding: 10px;
border-bottom: 2px #2a89a6 solid;
box-shadow: 0px 2px 5px 0px rgba(0,0,0,0.2);

}

.flex-grow {
flex-grow: 1;
}

/* Responsive adjustments */
@media (max-width: 640px) {
.container {
padding: 1rem;
}

.log-entry {
padding-left: 40px;
}
}

/* Time text alignment */
.text-sm br {
margin: 2px 0;
}
</style>

@section('content')
    <div class="container">
        <h2>Activity Logs</h2>

        <form action="{{ route('tenant.logs.index') }}" method="GET" id="filterForm">
            <div class="filters-main">
                {{-- Search Bar --}}
                <div class="search-wrapper">
                    <input
                            type="text"
                            class="search-input"
                            placeholder="Search by name"
                            name="search"
                            value="{{ request('search') }}"
                            onchange="document.getElementById('filterForm').submit()"
                    >
                    @if(request()->anyFilled(['search','action_type', 'entity_type']))
                        <a href="{{ route('tenant.logs.index') }}" class="clear-filters">
                            <i class="fas fa-times"></i> Clear All Filters
                        </a>
                    @endif
                </div>
            </div>
            <div class="filters-secondary">
                <div class="select-wrapper">
                    <select name="action_type" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">Filter by action type</option>
                        <option value="added" {{ request('action_type') == 'added' ? 'selected' : '' }}>Created</option>
                        <option value="updated" {{ request('action_type') == 'updated' ? 'selected' : '' }}>Updated</option>
                        <option value="deleted" {{ request('action_type') == 'deleted' ? 'selected' : '' }}>Deleted</option>
                    </select>
                </div>

                <div class="select-wrapper">
                    <select name="entity_type" class="filter-select" onchange="document.getElementById('filterForm').submit()">
                        <option value="">All Entries</option>
                        <option value="Asset" {{ request('entity_type') == 'Asset' ? 'selected' : '' }}>Asset</option>
                        <option value="AssetType" {{ request('entity_type') == 'AssetType' ? 'selected' : '' }}>Asset Type</option>
                        <option value="Invoice" {{ request('invoice') == 'User' ? 'selected' : '' }}>Invoice</option>
                        <option value="Project" {{ request('entity_type') == 'Project' ? 'selected' : '' }}>Project</option>
                        <option value="Quote" {{ request('quote') == 'User' ? 'selected' : '' }}>Quote</option>
                        <option value="Smmes" {{ request('smmes') == 'Smmes' ? 'selected' : '' }}>SMME</option>
                        <option value="User" {{ request('entity_type') == 'User' ? 'selected' : '' }}>User</option>
                    </select>
                </div>


            </div>
        </form>


        @foreach($entityLogs as $log)
            <div class="log-entry">
                <div class="flex">
                    <div class="text-sm">
                        {{ $log->created_at->format('d M Y') }}
                        <br>
                        {{ $log->created_at->format('h:i A') }}
                    </div>
                    <div class="flex-grow">
                        <h3 class="text-lg">
                            {{ $log->entity_type }} {{ $log->action_type }}
                        </h3>
                        <p class="text-gray-600">{{ $log->description }}</p>
                        <div class="text-gray-500">
                            @if( $log->additional_details )
                                {{$log->additional_details}}
                            @endif
                        </div>
                        <div class="text-gray-600-sm">
                            by {{ $log->performed_by }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
            {{ $entityLogs->links() }}
    </div>
@endsection