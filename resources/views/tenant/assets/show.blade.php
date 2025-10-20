@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Asset Details</h1>
    <div class="card">
        <div class="card-body">
            <form>
                <div class="row">
                    <div class="col-md-6">
                        <label for="name" class="form-label"><strong>Asset Name:</strong></label>
                        <input type="text" id="name" class="form-control" style="background-color: white;" value="{{ $asset->name }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="serial_number" class="form-label"><strong>Serial Number:</strong></label>
                        <input type="text" id="serial_number" class="form-control" style="background-color: white;" value="{{ $asset->serial_number }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="model" class="form-label"><strong>Model:</strong></label>
                        <input type="text" id="model" class="form-control" style="background-color: white;" value="{{ $asset->model }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label"><strong>Status:</strong></label>
                        <input type="text" id="status" class="form-control" style="background-color: white;" value="{{ $asset->status }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="cost" class="form-label"><strong>Cost:</strong></label>
                        <input type="text" id="cost" class="form-control" style="background-color: white;" value="R{{ number_format($asset->cost, 2) }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="location" class="form-label"><strong>Location:</strong></label>
                        <input type="text" id="location" class="form-control" style="background-color: white;" value="{{ $asset->location }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="purchase_date" class="form-label"><strong>Purchase Date:</strong></label>
                        <input type="text" id="purchase_date" class="form-control" style="background-color: white;" value="{{ $asset->purchase_date->format('Y-m-d') }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="warranty_date" class="form-label"><strong>Warranty Date:</strong></label>
                        <input type="text" id="warranty_date" class="form-control" style="background-color: white;" value="{{ $asset->warranty_date->format('Y-m-d') }}" readonly>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label for="created_at" class="form-label"><strong>Created At:</strong></label>
                        <input type="text" id="created_at" class="form-control" style="background-color: white;" value="{{ $asset->created_at }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="updated_at" class="form-label"><strong>Updated At:</strong></label>
                        <input type="text" id="updated_at" class="form-control" style="background-color: white;" value="{{ $asset->updated_at }}" readonly>
                    </div>
                </div>

                <h2 class="mt-4">Uploaded Images</h2>
                <div class="d-flex flex-wrap">
                    @if ($asset->picture_paths && count(json_decode($asset->picture_paths)) > 0)
                        @foreach (json_decode($asset->picture_paths) as $picturePath)
                            @if ($picturePath)
                                <div class="m-2 text-center">
                                    <img src="{{ asset($picturePath) }}"
                                         alt="Asset Picture"
                                         class="img-thumbnail shadow-sm"
                                         style="width: 200px; height: auto; border-radius: 10px; object-fit: cover;"
                                         loading="lazy"
                                         onerror="this.onerror=null; this.src='{{ asset('images/no-image.png') }}';">
                                </div>
                            @endif
                        @endforeach
                    @else
                        <p class="text-muted">No images uploaded.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <br>

    @include('partials.icon_button', ['href' => route('tenant.assets.edit', $asset), 'type' => 'primary', 'icon' => 'fa-pen-to-square', 'slot' => 'Edit'])
    @include('partials.icon_button', ['href' => route('tenant.assets.index'), 'type' => 'danger', 'icon' => 'fa-arrow-left', 'slot' => 'Back'])

    <form action="{{ route('tenant.assets.destroy', $asset) }}" method="POST" style="display: inline-block;">
        @csrf
    </form>
</div>
@endsection
