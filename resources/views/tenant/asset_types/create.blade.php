@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Create New Asset Type</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tenant.asset-types.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-default">
                <i class="fa fa-arrow-left"></i>
                Back to Asset Types</a>
            <button type="submit" class="btn btn-primary">Create Asset Type</button>
        </form>
    </div>
@endsection
