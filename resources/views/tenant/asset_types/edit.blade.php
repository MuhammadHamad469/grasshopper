@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Edit Asset Type</h2>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('tenant.asset-types.update', $assetType) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $assetType->name) }}" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $assetType->description) }}</textarea>
            </div>
            @include('partials.save-button')
            @include('partials.icon_button', [
                'href' => route('tenant.asset-types.index'),
                'type' => 'default',
                'icon' => 'fa-arrow-left',
                'slot' => 'Back',
            ])
        </form>
    </div>
@endsection
