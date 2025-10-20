@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Location Details</h2>
        <a href="{{ route('tenant.locations.index') }}" class="btn btn-secondary mb-3">Back to Locations</a>

        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $location->name }}</h5>
                <p class="card-text"><strong>Created at:</strong> {{ $location->created_at->format('Y-m-d H:i:s') }}</p>
                <p class="card-text"><strong>Last updated:</strong> {{ $location->updated_at->format('Y-m-d H:i:s') }}</p>
            </div>
        </div>

        <div class="mt-3">
            <a href="{{ route('tenant.locations.edit', $location) }}" class="btn btn-warning">Edit</a>
            <form action="{{ route('tenant.locations.destroy', $location) }}" method="POST" style="display: inline-block;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this location?')">Delete</button>
            </form>
        </div>
    </div>
@endsection