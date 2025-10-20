<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class FailedLoginNotification extends Notification
{
	use Queueable;

	protected $ipAddress;
	protected $userAgent;

	public function __construct($ipAddress, $userAgent)
	{
		$this->ipAddress = $ipAddress;
		$this->userAgent = $userAgent;
	}

	public function via($notifiable)
	{
		return ['mail'];
	}

	public function toMail($notifiable)
	{
		return (new MailMessage)
				->subject('Failed Login Attempt')
				->view(
						'emails.failed-login',
						[
								'user' => $notifiable,
								'ipAddress' => $this->ipAddress,
								'userAgent' => $this->userAgent,
								'time' => now()->format('Y-m-d H:i:s'),
						]
				);
	}

	public function toArray($notifiable)
	{
		return [];
	}
}