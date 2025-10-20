@php
    use Illuminate\Support\Str;
@endphp
@extends('layouts.app')

@section('content')
    <div class="card-no-b">
        <div class="card-body">
            @include('tenant.projects.partials.header', ['project' => $project])
        </div>

    </div>


    <div class="row mb-4">

        @php
            $projectStatus = 'Planned';
            if ($project->status == 2){
                $projectStatus = 'Ongoing';
			}
			if ($project->status == 3){
                $projectStatus = 'Complete';
			}
        @endphp
        @include('tenant.projects.partials.card', ['title'=>$projectStatus, 'text'=>'Status'])
        @include('tenant.projects.partials.card', ['title'=>$project->location->name, 'text'=>'Location'])
        @include('tenant.projects.partials.card', ['title'=>$teamLeaderName, 'text'=>'Team Leader'])
        @include('tenant.projects.partials.progress',['progress'=>$progress['actualProgressPercentage']])
    </div>


    <div class="card mt-4">
        <div class="card-body">
            <h4 class="mb-4 text-muted-sub">Project Details</h4>
            <div class="row">

                <div class="col-md-4">
                    <h5 class="mb-3">Assets</h5>
                    @if($project->assets->isNotEmpty())
                        <ul class="list-group asset-list">
                            @foreach($project->assets as $asset)
                                <li class="list-group-item">
                                    <strong>{{ $asset->name }}</strong>
                                    <small class="d-block text-muted">SN: {{ $asset->serial_number }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted">No assets assigned to this project.</p>
                    @endif
                </div>

                <div class="col-md-4">
                    <h5 class="mb-3">Description</h5>
                    <p class="mb-4"> {{ Str::limit($project->description, 200) }}</p>

        
            </div>
        </div>
    </div>

       <!-- Budget Display -->
 <div class="row mb-4">
    <div class="col-md-6">
        <div class="form-group">
            <label for="budget">Budget (R)</label>
            <input type="number" class="form-control form-control-sm input" placeholder="Budget"
                   id="budget" name="budget" step="0.01" value="{{ old('budget', $project->budget) }}" required>
        </div>
    </div>
</div>

<!-- Show Budget Value -->
@if($project->budget)
    <div class="alert alert-info">
        <strong>Budget:</strong> R{{ number_format($project->budget, 2) }}
    </div>
@endif

    {{-- @if($smme)
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="mb-4 text-muted-sub">SMME Details</h4>
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Basic Information</h5>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Name:</b> {{ $smme->name }}
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Registration Number:</b> {{ $smme->registration_number }}
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Years of Experience:</b> {{ $smme->years_of_experience }}
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Additional Details</h5>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Team Composition:</b> {{ $smme->team_composition }}
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Grade:</b> {{ $smme->grade }}
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Status:</b> <span class="badge {{ $smme->status == 'Active' ? 'badge-success' : 'badge-danger' }}">
                                {{ $smme->status }}
                            </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>Documents Verified:</b>
                                <span class="badge {{ $smme->documents_verified ? 'badge-success' : 'badge-warning' }}">
                                {{ $smme->documents_verified ? 'Yes' : 'No' }}
                            </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card mt-4">
            <div class="card-body">
                <h4 class="mb-4 text-muted-sub">SMME Details</h4>
                <p class="text-muted">No SMME associated with this project.</p>
            </div>
        </div>
    @endif --}}

    <br>

    @include('partials.icon_button', ['href' => route('tenant.projects.edit', $project), 'type' => 'primary', 'icon' => 'fa-pen-to-square', 'slot' => 'Edit'])
    @include('partials.icon_button', ['href' => route('tenant.projects.index'), 'type' => 'danger', 'icon' => 'fa-arrow-left', 'slot' => 'Back'])
@endsection