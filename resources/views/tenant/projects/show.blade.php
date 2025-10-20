@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Project Details</h1>
        <div class="card">
            <div class="card-body">
                <form>
                    <div class="row">
                        <div class="col-md-6 readonly-input">
                            <label for="project_name" class="form-label"><strong>Project Name:</strong></label>
                            <input type="text" id="project_name" class="form-control readonly-input"
                                   style="background-color: white;" value="{{ $project->project_name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="project_type" class="form-label"><strong>Project Type:</strong></label>
                            <input type="text" id="project_type" class="form-control"
                                   style="background-color: white;" value="{{ $project->projectType->name }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label"><strong>Start Date:</strong></label>
                            <input type="text" id="start_date" class="form-control"
                                   style="background-color: white;" value="{{ $project->startDate }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label"><strong>End Date:</strong></label>
                            <input type="text" id="end_date" class="form-control"
                                   style="background-color: white;" value="{{ $project->endDate }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="budget" class="form-label"><strong>Budget (R):</strong></label>
                            <input type="text" id="budget" class="form-control"
                                   style="background-color: white;" value="{{ number_format($project->budget, 2) }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label"><strong>Status:</strong></label>
                            <input type="text" id="status" class="form-control"
                                   style="background-color: white; color:
                                       @if ($project->status == 1) blue
                                       @elseif ($project->status == 2) orange
                                       @elseif ($project->status == 3) green
                                       @endif;"
                                   value="{{ $project->status == 1 ? 'Planned' : ($project->status == 2 ? 'Ongoing' : 'Complete') }}" readonly>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <label for="description" class="form-label"><strong>Description:</strong></label>
                            <textarea id="description" class="form-control"
                                      style="background-color: white;" rows="3"
                                      readonly>{{ $project->description }}</textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <label for="location" class="form-label"><strong>Location:</strong></label>
                            <input type="text" id="location" class="form-control"
                                   style="background-color: white;" value="{{ $project->location->name }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="team_leader" class="form-label"><strong>Team Leader:</strong></label>
                            <input type="text" id="team_leader" class="form-control"
                                   style="background-color: white;" value="{{ $project->teamLeader->name }}" readonly>
                        </div>
                    </div>

                    @if($project->projectType->id == 1)
                        <!-- Vegetation Management Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <label for="target_hectares" class="form-label"><strong>Target Hectares:</strong></label>
                                <input type="text" id="target_hectares" class="form-control"
                                       style="background-color: white;" value="{{ $project->target_hectares }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label for="actual_hectares" class="form-label"><strong>Actual Hectares Completed:</strong></label>
                                <input type="text" id="actual_hectares" class="form-control"
                                       style="background-color: white;" value="{{ $project->actual_hectares }}" readonly>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label"><strong>Checklist:</strong></label>
                                <div class="form-control" style="background-color: white;">
                                    <strong>Quote:</strong> {!! $project->quote_check == 1 ? '✅' : '❌' !!} |
                                    <strong>Inspection:</strong> {!! $project->inspection_check == 1 ? '✅' : '❌' !!} |
                                    <strong>Labour Report:</strong> {!! $project->labour_report_check == 1 ? '✅' : '❌' !!} |
                                    <strong>Safety Talk:</strong> {!! $project->safety_talk_check == 1 ? '✅' : '❌' !!} |
                                    <strong>Herbicide Usage:</strong> {!! $project->herbicide_check == 1 ? '✅' : '❌' !!} |
                                    <strong>Invoice:</strong> {!! $project->invoice_check == 1 ? '✅' : '❌' !!}
                                </div>

                            </div>
                        </div>
                    @endif

                    @if($project->projectType->id == 2)
                        <!-- Training Fields -->
                        <div class="row">
                            <div class="col-md-6">
                                <label for="number_of_students" class="form-label"><strong>Number of Students:</strong></label>
                                <input type="text" id="number_of_students" class="form-control"
                                       style="background-color: white;" value="{{ $project->number_of_students }}" readonly>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label"><strong>Checklist:</strong></label>
                                <div class="form-control" style="background-color: white;">
                                    <strong>Facilitation: {!! $project->facilitation_check == 1 ? '✅' : '❌' !!} |</strong>
                                    <strong>Assessment: {!! $project->assessment_check == 1 ? '✅' : '❌' !!} |</strong>
                                    <strong>Moderation: {!! $project->moderation_check == 1 ? '✅' : '❌' !!} |</strong>
                                    <strong>Database Administration: {!! $project->database_admin_check == 1 ? '✅' : '❌' !!} |</strong>
                                    <strong>Certification:{!! $project->certification_check == 1 ? '✅' : '❌' !!} </strong>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <label class="form-label"><strong>Progress:</strong></label>
                                @php
                                    $totalCheckboxes = 5;
                                    $checkedCount = ($project->facilitation_check == 1 ? 1 : 0) +
                                                    ($project->assessment_check == 1 ? 1 : 0) +
                                                    ($project->moderation_check == 1 ? 1 : 0) +
                                                    ($project->database_admin_check == 1 ? 1 : 0) +
                                                    ($project->certification_check == 1 ? 1 : 0);
                                    $progressPercent = round(($checkedCount / $totalCheckboxes) * 100);
//																		dd($checkedCount);
                                @endphp
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar"
                                         style="width: {{ $progressPercent }}%;"
                                         aria-valuenow="{{ $progressPercent }}"
                                         aria-valuemin="0"
                                         aria-valuemax="100">{{ $progressPercent }}%</div>
                                </div>
                            </div>
                        </div>

                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <label for="created_at" class="form-label"><strong>Created At:</strong></label>
                            <input type="text" id="created_at" class="form-control"
                                   style="background-color: white;" value="{{ $project->created_at }}" readonly>
                        </div>
                        <div class="col-md-6">
                            <label for="updated_at" class="form-label"><strong>Updated At:</strong></label>
                            <input type="text" id="updated_at" class="form-control"
                                   style="background-color: white;" value="{{ $project->updated_at }}" readonly>
                        </div>
                    </div>

                    <div class="row mt-3">

                        <div class="col-md-12">
                            <p class="text"><strong>Assets:</strong></p>
                            <table class="table table-hover table-bordered">
                                <thead class="thead-dark">
                                <tr>
                                    <th>Name</th>
                                    <th>Serial Number</th>
                                    <th>Model</th>
                                    <th>Purchase Date</th>
                                    <th>Warranty Date</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($project->assets as $asset)
                                    <tr>
                                        <td>{{ $asset->name }}</td>
                                        <td>{{ $asset->serial_number }}</td>
                                        <td>{{ $asset->model }}</td>
                                        <td>{{ $asset->purchase_date->format('Y-m-d') }}</td>
                                        <td>{{ $asset->warranty_date->format('Y-m-d') }}</td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">No assets found for this project.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <br>

        @include('partials.icon_button', ['href' => route('tenant.projects.edit', $project), 'type' => 'primary', 'icon' => 'fa-pen-to-square', 'slot' => 'Edit'])
        @include('partials.icon_button', ['href' => route('tenant.projects.index'), 'type' => 'danger', 'icon' => 'fa-arrow-left', 'slot' => 'Back'])

    </div>
@endsection