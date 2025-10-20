@extends('emails.layouts.master')

@section('title')
    Failed Login Attempt
@endsection

@section('content')
    <p class="text">Hey {{ $user->name }},</p>

    <p class="text">We detected a failed login attempt on your account.</p>

    <div class="details-box">
        <h3 class="details-heading">Details</h3>
        <p class="text"><strong>Time:</strong> {{ $time }}</p>
        <p class="text"><strong>IP Address:</strong> {{ $ipAddress }}</p>
        <p class="text"><strong>Browser:</strong> {{ $userAgent }}</p>
    </div>

    <p class="text">If this was you, you can ignore this message. If you don't recognize this login attempt, please change your password immediately.</p>

    <div class="button-container">
        <a href="{{ route('auth.password.reset') }}" class="button">
            Reset Password
        </a>
    </div>
@endsection