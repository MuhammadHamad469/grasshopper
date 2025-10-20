@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Location</h2>
        <a href="{{ route('tenant.locations.index') }}" class="btn btn-secondary mb-3">Back to Locations</a>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tenant.locations.update', $location) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $location->name) }}" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Location</button>
        </form>
    </div>
@endsection