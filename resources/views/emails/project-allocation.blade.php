@extends('emails.layouts.master')

@section('title')
    New Project Allocation
@endsection

@section('content')
    <p class="text">Dear {{ $notifiable->name }},</p>

    <p class="text">{{ $assignerName }} appointed you as the team leader of a new project.</p>

    <div class="details-box">
        <h3 class="details-heading">Project Details:</h3>
        <p class="text"><strong>Project Name:</strong> {{ $project->project_name }}</p>
        <p class="text"><strong>Start Date:</strong> {{ $project->startDate }}</p>
        <p class="text"><strong>End Date:</strong> {{ $project->endDate }}</p>
    </div>

    <div class="button-container">
        <a href="{{ route('tenant.projects.show', $project->id) }}" class="button">
            View Project Details
        </a>
    </div>

    <p class="text">If you have any questions, please don't hesitate to contact your manager.</p>
@endsection