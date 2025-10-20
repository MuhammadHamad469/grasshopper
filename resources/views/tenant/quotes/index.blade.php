@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Quotes</h2>
        <div>

        <div class="quote-filters">
            <div class="filters-header">
                <a href="{{ route('tenant.quotes.create') }}" class="new-quote-button">
                    <i class="fas fa-add"></i> New Quote
                </a>
                <a href="{{ route('tenant.quotes.export', request()->query()) }}" class="new-quote-button">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </a>
            </div>
        </div>

        <form action="{{route('tenant.quotes.index') }}" method="GET" id="filterForm">
        <div class="filters-main">
                        {{-- Search Bar --}}
             <div class="search-wrapper">
                <input
                    type="text"
                    class="search-input"
                    placeholder="Search by client name"
                    name="search"
                    value="{{ request('search') }}"
                    >
                <button type="submit" class="search-button">
                    <i class="fas fa-search"></i>
                </button>
                @if(request()->anyFilled(['search', 'total_amount_range', 'issue_date', 'expiry_date']))
                    <a href="{{ route('tenant.quotes.index') }}" class="clear-filters">
                        <i class="fas fa-times"></i> Clear All Filters
                    </a>
                @endif 
            </div>
        </div> 
        <div class="filters-secondary">
        {{-- Date Range for Issue Date --}}
        <div class="select-wrapper">
        <select name="issue_date" class="filter-select" onchange="document.getElementById('filterForm').submit()">
            <option value="">Filter by Issue Date</option>
            <option value="last_7_days" {{ request('issue_date') == 'last_7_days' ? 'selected' : '' }}>Last 7 Days</option>
            <option value="last_30_days" {{ request('issue_date') == 'last_30_days' ? 'selected' : '' }}>Last 30 Days</option>
            <option value="last_6_months" {{ request('issue_date') == 'last_6_months' ? 'selected' : '' }}>Last 6 Months</option>
            <option value="last_year" {{ request('issue_date') == 'last_year' ? 'selected' : '' }}>Last Year</option>
        </select>
        </div>

        {{-- Date Range for Expiry Date --}}
        <div class="select-wrapper">
        <select name="expiry_date" class="filter-select" onchange="document.getElementById('filterForm').submit()">
            <option value="">Filter by Expiry Date</option>
            <option value="next_7_days" {{ request('expiry_date') == 'next_7_days' ? 'selected' : '' }}>Next 7 Days</option>
            <option value="next_30_days" {{ request('expiry_date') == 'next_30_days' ? 'selected' : '' }}>Next 30 Days</option>
            <option value="next_6_months" {{ request('expiry_date') == 'next_6_months' ? 'selected' : '' }}>Next 6 Months</option>
            <option value="next_year" {{ request('expiry_date') == 'next_year' ? 'selected' : '' }}>Next Year</option>
        </select>
        </div>
    
        
        {{-- Total amount Filter --}}
        <div class="select-wrapper">
        <select name="total_amount_range" class="filter-select" onchange="document.getElementById('filterForm').submit()">
        <option value="">Filter by amount range</option>
            <option value="0-50000" {{ request('total_amount_range') == '0-50000' ? 'selected' : '' }}>R0 - R50000</option>
            <option value="50001-100000" {{ request('total_amount_range') == '50001-100000' ? 'selected' : '' }}>R50001-R100000</option>
            <option value="100001-150000" {{ request('total_amount_range') == '100001-150000' ? 'selected' : '' }}>R100001-R150000</option>
            <option value="150000+" {{ request('total_amount_range') == '150000+' ? 'selected' : '' }}>More than R150000</option>
        </select>
        </div>
</div>
</form>
        </div>
    </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>Quote Number</th>
                <th>Client Name</th>
                <th>Issue Date</th>
                <th>Expiry Date</th>
                <th>Total Amount</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($quotes as $quote)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $quote->quote_number }}</td>
                    <td>{{ $quote->client_name }}</td>
                    <td>{{ $quote->issue_date instanceof \DateTime ? $quote->issue_date->format('Y-m-d') : $quote->issue_date }}</td>
                    <td>{{ $quote->expiry_date instanceof \DateTime ? $quote->expiry_date->format('Y-m-d') : $quote->expiry_date }}</td>
                    <td>{{ number_format($quote->total_amount, 2) }}</td>
                    <td>
                        <a href="{{ route('tenant.quotes.show', $quote) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('tenant.quotes.edit', $quote) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('tenant.quotes.destroy', $quote) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this quote?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No quotes found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $quotes->links() }}
    </div>

    <style>
        .quote-filters {
            background-color: #f8f9fa;
            padding: 1.25rem;
            border-radius: 4px;
        }

        .filters-header {
            margin-bottom: 1rem;
        }

        .new-quote-button {
            display: inline-block;
            padding: 0.6rem 1.25rem;
            background-color: #4a90e2;
            color: white;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background-color 0.2s;
        }

        .new-quote-button:hover {
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