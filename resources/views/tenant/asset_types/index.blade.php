@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Asset Types</h2>
        <a href="{{ route('tenant.asset-types.create') }}" class="btn btn-primary mb-3">Create New Asset Type</a>

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
            @forelse($assetTypes as $assetType)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $assetType->name }}</td>
                    <td>{{ $assetType->description }}</td>
                    <td>{{ $assetType->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $assetType->updated_at->format('Y-m-d H:i') }}</td>
                    <td>
                        <a href="{{ route('tenant.asset-types.show', $assetType) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                        <a href="{{ route('tenant.asset-types.edit', $assetType) }}" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('tenant.asset-types.destroy', $assetType) }}" method="POST" style="display: inline-block;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this asset type?')"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No asset types found.</td>
                </tr>
            @endforelse
            </tbody>
        </table>

        {{ $assetTypes->links() }}
    </div>
@endsection