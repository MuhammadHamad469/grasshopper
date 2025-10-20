@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Asset Type Details</h1>
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name" class="form-label"><strong>Asset Type Name:</strong></label>
                            <input type="text" id="name" class="form-control" style="background-color: white;" value="{{ $assetType->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="description" class="form-label"><strong>Description:</strong></label>
                            <textarea id="description" class="form-control" style="background-color: white;" rows="3" readonly>{{ $assetType->description }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="created_at" class="form-label"><strong>Created At:</strong></label>
                            <input type="text" id="created_at" class="form-control" style="background-color: white;" value="{{ $assetType->created_at }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="updated_at" class="form-label"><strong>Updated At:</strong></label>
                            <input type="text" id="updated_at" class="form-control" style="background-color: white;" value="{{ $assetType->updated_at }}" readonly>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>

        @include('partials.icon_button', ['href' => route('tenant.asset-types.edit', $assetType), 'type' => 'primary', 'icon' => 'fa-pen-to-square', 'slot' => 'Edit'])
        @include('partials.icon_button', ['href' => route('tenant.asset-types.index'), 'type' => 'danger', 'icon' => 'fa-arrow-left', 'slot' => 'Back'])

        <form action="{{ route('tenant.asset-types.destroy', $assetType) }}" method="POST" style="display: inline-block;">
            @csrf
        </form>

    </div>
@endsection