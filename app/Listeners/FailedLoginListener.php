<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Notifications\FailedLoginNotification;

class FailedLoginListener
{
	public function handle(Failed $event)
	{
		if ($event->user) {
			$cacheKey = 'failed_login_' . $event->user->id;

			// Only send notification if we haven't sent one in the last 30 minutes
			if (!Cache::has($cacheKey)) {
				try {
					$ipAddress = request()->ip();
					$userAgent = request()->userAgent();
					Log::warning('Failed login attempt', [
							'user_id' => $event->user->id,
							'email' => $event->user->email,
							'ip' => $ipAddress,
							'user_agent' => $userAgent
					]);

					Log::channel('notifications')->info(__class__ . ' '. __function__ . ": Sending failed login attempt notification to user id: {$event->user->id} email: {$event->user->email}');");
					$event->user->notify(new FailedLoginNotification($ipAddress, $userAgent));

					Cache::put($cacheKey, true, now()->addMinutes(30));

				} catch (\Exception $e) {
					Log::error('Failed to send login failure notification', [
							'user_id' => $event->user->id,
							'error' => $e->getMessage()
					]);
					Log::channel('notifications')->error('Failed to send login failure notification', [
							'user_id' => $event->user->id,
							'error' => $e->getMessage()
					]);
				}
			}
		}
	}
}