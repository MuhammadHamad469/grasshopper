@extends('emails.layouts.master')

@section('title')
    Welcome to {{ config('app.name') }} – Let’s Get Started!
@endsection

@section('content')
    <p class="text">Hey {{ $user->name }},</p>

    <p class="text">🎉 Welcome to {{ config('app.name') }}! We’re thrilled to have you on board. Your account has been successfully created, and you’re all set to dive in.</p>

    <div class="details-box">
        <h3 class="details-heading">Your Login Details</h3>
        <p class="text"><strong>Email:</strong> {{ $user->email }}</p>
        <p class="text"><strong>Temporary Password:</strong> {{ $password }}</p>
    </div>

    <p class="text">🔒 For your security, we recommend changing your password after your first login. Safety first, always!</p>

    <div class="button-container">
        <a href="{{ route('login') }}" class="button">
            Let’s Go – Login Now!
        </a>
    </div>

    <p class="text">If you have any questions or need assistance, feel free to reach out. We’re here to help!</p>
@endsection