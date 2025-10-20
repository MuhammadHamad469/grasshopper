<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Notifications\FailedLoginNotification;
use Illuminate\Auth\Events\Logout;
use App\Models\UserSession;
use Carbon\Carbon;

class TrackUserLogoutSession
{
	public function handle(Logout $event)
    {
        $sessionId = session('current_user_session_id');
        if ($sessionId) {
            $session = UserSession::find($sessionId);
            if ($session && !$session->logout_time) {
                $logout                    = now();
                $session->logout_time      = $logout;
                $session->duration_seconds = $logout->diffInSeconds($session->login_time);
                $session->save();
            }
        }
    }
}