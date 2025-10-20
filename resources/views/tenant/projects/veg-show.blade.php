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
                    <div class="mb-3">
                        <h4 class="mb-0">{{ $progress['currentDay'] ?? $project->current_day }}</h4>
                        <p class="text-muted-sm mb-0">Current Day</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div>
                        <h4 class="mb-0">{{ number_format($progress['expectedProgress'] ?? $project->expected_progress, 2) }}</h4>
                        <p class="text-muted-sm mb-0">Expected Progress (ha)</p>
                    </div>
                    <div class="mb-3">
                        <h4 class="mb-0">{{ number_format($project->actual_hectares,2) }}</h4>
                        <p class="text-muted-sm mb-0">Actual Progress (ha)</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div>
                        <h4 class="mb-0">{{ number_format($progress['expectedProgressPercentage'] , 0) }}</h4>
                        <p class="text-muted-sm mb-0">Expected Progress (%)</p>
                    </div>
                    <div class="mb-3">
                        <h4 class="mb-0">{{ number_format($progress['differenceFromTarget'] , 0) }}
                        </h4>
                        <p class="text-muted-sm mb-0">Difference from Target (%)</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <h4 class="mb-0">{{ number_format($project->target_hectares , 0) }}
                        </h4>
                        <p class="text-muted-sm mb-0">Target Hectares (ha)</p>
                    </div>
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

                    <h5 class="mb-3">Vehicle KMS</h5>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <div>
                                <strong>Target:</strong>
                                <p class="mb-0">{{ number_format($project->vehicle_kms_target) }} km</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div>
                                <strong>Actual:</strong>
                                <p class="mb-0">{{ number_format($project->actual_vehicle_kms) }} km</p>
                            </div>
                        </div>


                    </div>
                    <div class="progress">
                        @php
                            $kmsPercentage = $project->vehicle_kms_target > 0
                                ? ($project->actual_vehicle_kms / $project->vehicle_kms_target) * 100
                                : 0;
                        @endphp
                        <div class="progress-bar" role="progressbar"
                             style="width: {{ $kmsPercentage }}%;"
                             aria-valuenow="{{ $kmsPercentage }}"
                             aria-valuemin="0"
                             aria-valuemax="100">
                            {{ number_format($kmsPercentage, 1) }}%
                        </div>
                    </div>
                </div>


                <div class="col-md-4">
                    <h5 class="mb-3">Checklist</h5>
                    <ul class="list-group">
                        @php
                            $checklistItems = [
                                'Quote' => $project->quote_check,
                                'Inspection' => $project->inspection_check,
                                'Labour Report' => $project->labour_report_check,
                                'Safety Talk' => $project->safety_talk_check,
                                'Herbicide Usage' => $project->herbicide_check,
                                'Invoice' => $project->invoice_check
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

    {{-- @if($smme && !empty($smme) && is_object($smme))
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
                            <b>Status:</b>
                            <span class="badge {{ $smme->status == 'Active' ? 'badge-success' : 'badge-danger' }}">
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
@endif --}}


<div class="card mt-4">
    <div class="card-body">
        <h4 class="mb-4 text-muted-sub">Project Documents</h4>
        @if($project->documents !== null && $project->documents->isNotEmpty())
    <ul class="list-group">
        @foreach($project->documents as $document)
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="{{ Storage::url($document->file_path) }}" target="_blank">{{ $document->file_name }}</a>
            </li>
        @endforeach
    </ul>
@else
            <p class="text-muted">No documents uploaded for this project.</p>
        @endif
    </div>
</div>

<br>

@include('partials.icon_button', ['href' => route('tenant.projects.edit', $project), 'type' => 'primary', 'icon' => 'fa-pen-to-square', 'slot' => 'Edit'])
@include('partials.icon_button', ['href' => route('tenant.projects.index'), 'type' => 'danger', 'icon' => 'fa-arrow-left', 'slot' => 'Back'])

@endsection