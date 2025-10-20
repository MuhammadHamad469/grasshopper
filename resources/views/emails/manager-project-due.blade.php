@extends('emails.layouts.master')

@section('title')
    Project Due Date Reminder
@endsection

@section('content')
    <p class="text">Dear {{ $notifiable->name }},</p>

    <p class="text">This is a reminder that the following project, assigned to <strong>{{$project->teamLeader->name}}</strong>, is due in 2 days:</p>

    <div class="details-box">
        <h3 class="details-heading">Project Details:</h3>
        <p class="text"><strong>Project Name:</strong> {{ $project->project_name }}</p>
        <p class="text"><strong>End Date:</strong> {{ $project->endDate }}</p>
        <p class="text"><strong>Expected Progress:</strong> {{ $expectedProgress }}%</p>
        <p class="text"><strong>Current Progress:</strong> {{ $currentProgress }}%</p>
    </div>

    <div class="button-container">
        <a href="{{ route('tenant.projects.show', $project->id) }}" class="button">
            View Project Status
        </a>
    </div>

    <p class="text">The project deadline is approaching, and the current progress has not yet been updated. Please review and take any necessary actions to ensure the project stays on track.</p>
@endsection