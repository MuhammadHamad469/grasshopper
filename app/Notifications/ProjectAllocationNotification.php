<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectAllocationNotification extends Notification
{
	use Queueable;

	protected $project;

	public function __construct(Project $project)
	{
		$this->project = $project;
	}

	public function via($notifiable)
	{
		return ['mail'];
	}

	public function toMail($notifiable)
	{
		$assignerName = Auth::user()->name;
		return (new MailMessage)
				->subject('New Project Allocation')
				->view(
						'emails.project-allocation',
						[
								'project' => $this->project,
								'notifiable' => $notifiable,
								'assignerName' => $assignerName,
						]
				);
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [
			//
		];
	}
}