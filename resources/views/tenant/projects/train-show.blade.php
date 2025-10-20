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
            <h4 class="mb-3 text-muted-sub">Project Progress</h4>
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <h4 class="mb-0">{{ $progress['totalDays'] ?? $project->total_days }}</h4>
                        <p class="text-muted-sm mb-0">Planned Days</p>
                    </div>
                </div> <!-- Closing div added here -->
                <div class="col-md-3">
                    <div>
                        <h4 class="mb-0 text-{{ ($progress['status'] ?? $project->status) == 'Behind schedule' ? 'danger' : 'success' }}">
                            {{ $progress['status'] ?? $project->status }}
                        </h4>
                        <p class="text-muted-sm mb-0">Progress</p>
                    </div>
                </div>
            </div>
        </div>
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

    
    <div class="card mt-4">
        <div class="card-body">
            <h4 class="mb-4 text-muted-sub">Training Details</h4>
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">Basic Information</h5>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>Number of students to be demonstrated:</b> 
                            <span>{{ $project->number_of_students }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>Budget:</b> 
                            <span>{{ $project->budget }}</span>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">Facilitator Information & Checklist</h5>
                    <ul class="list-group">
                        <!-- Facilitator Information -->
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <p class="card-text"><strong>Facilitator Name:</strong> {{ $project->facilitator_name }}</p>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <b>Experience CV:</b>
                            @if ($project->experience_cv)
                            <a href="https://{{ env('AWS_BUCKET') }}.s3.{{ env('AWS_DEFAULT_REGION') }}.amazonaws.com/{{ $project->experience_cv }}" target="_blank">View CV</a>
                        @else
                            <span class="badge badge-pill badge-secondary">Pending</span>
                        @endif
                        </li>
                
                        <!-- Checklist -->
                        @php
                            $checklistItems = [
                                'Facilitation' => $project->facilitation_check,
                                'Assessment' => $project->assessment_check,
                                'Moderation' => $project->moderation_check,
                                'Database Admin' => $project->database_admin_check,
                                'Certification' => $project->certification_check
                            ];
                        @endphp
                        @foreach($checklistItems as $item => $filePath)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <b>{{ $item }}:</b>
                                @if ($filePath)
                                    <a href="https://{{ env('AWS_BUCKET') }}.s3.{{ env('AWS_DEFAULT_REGION') }}.amazonaws.com/{{ $filePath }}" target="_blank">View Document</a>
                                @else
                                    <span class="badge badge-pill badge-secondary">Pending</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
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

    <br>

    @include('partials.icon_button', ['href' => route('tenant.projects.edit', $project), 'type' => 'primary', 'icon' => 'fa-pen-to-square', 'slot' => 'Edit'])
    @include('partials.icon_button', ['href' => route('tenant.projects.index'), 'type' => 'danger', 'icon' => 'fa-arrow-left', 'slot' => 'Back'])
@endsection