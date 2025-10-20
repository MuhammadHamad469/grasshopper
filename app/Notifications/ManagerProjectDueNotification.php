<?php

namespace App\Notifications;

use App\Repositories\ProjectRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Project;

class ManagerProjectDueNotification extends Notification
{
    use Queueable;

	protected $project;
	protected ProjectRepositoryInterface $projectRepository;

	public function __construct(Project $project, ProjectRepositoryInterface $projectRepository)
	{
		$this->project = $project;
		$this->projectRepository = $projectRepository;
	}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
		{
			$projectProgress = $this->projectRepository->calculateProjectProgress($this->project);
			$expectedProgress = number_format($projectProgress['expectedProgressPercentage'], 2) ?? 0;
			$currentProgress = number_format($projectProgress['actualProgressPercentage'], 2) ?? 0;

			return (new MailMessage)
					->subject('Project Due')
					->view(
							'emails.manager-project-due',
							[
									'project' => $this->project,
									'notifiable' => $notifiable,
									'expectedProgress' => $expectedProgress,
									'currentProgress' => $currentProgress,
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