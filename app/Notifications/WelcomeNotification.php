<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;

class WelcomeNotification extends Notification
{
	use Queueable;

	protected $password;

	public function __construct($password)
	{
		$this->password = $password;
	}

	public function via($notifiable)
	{
		return ['mail'];
	}

	public function toMail($notifiable)
	{
		return (new MailMessage)
				->subject('Welcome to ' . config('app.name'))
				->view(
						'emails.welcome',
						[
								'user' => $notifiable,
								'password' => $this->password,
						]
				);
	}

	public function toArray($notifiable)
	{
		return [];
	}
}