<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Notifications\FailedLoginNotification;
use Illuminate\Auth\Events\Login;
use App\Models\UserSession;
use Carbon\Carbon;

class TrackUserLoginSession
{
	public function handle(Login $event)
    {
        $session = UserSession::create([
			'user_id'    => $event->user->id,
			'login_time' => now(),
        ]);

        // Store the session ID in Laravel session for later
        session(['current_user_session_id' => $session->id]);
    }
}