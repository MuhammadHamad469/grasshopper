@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Locations</h2>
        <a href="{{ route('tenant.locations.create') }}" class="btn btn-primary mb-3">Create New Location</a>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-hover">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Updated At</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($locations as $location)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $location->name }}</td>
                    <td>{{ $location->description ?? 'N/A' }}</td>
                    <td>{{ $location->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $location->updated_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('tenant.locations.show', $location) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('tenant.locations.edit', $location) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('tenant.locations.destroy', $location) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this location?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No locations found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $locations->links() }}
    </div
@endsection