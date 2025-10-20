<?php

namespace App\Console\Commands;

use App\Notifications\ManagerProjectDueNotification;
use App\Notifications\ProjectDueNotification;
use Illuminate\Console\Command;
use App\Models\Project;
use Carbon\Carbon;
use App\Repositories\ProjectRepositoryInterface;
use Illuminate\Support\Facades\Log;

class SendProjectDueNotifications extends Command
{
	protected $signature = 'notifications:project-due';
	protected $description = 'Send notifications for projects due in 2 days';

	protected $projectRepository;

	public function __construct(ProjectRepositoryInterface $projectRepository)
	{
		parent::__construct();
		$this->projectRepository = $projectRepository;
	}

	public function handle()
	{
		$this->alert('Starting project due notifications process');
		Log::channel('notifications')->info('Starting project due notifications process');

		$twoDaysFromNow = Carbon::now()->addDays(2)->startOfDay();

		$projects = Project::whereDate('endDate', $twoDaysFromNow)
				->where('status', '!=', 'completed')
				->get();

		$this->info('Found ' . $projects->count() . ' projects due in 2 days');
		Log::channel('notifications')->info('Found ' . $projects->count() . ' projects due in 2 days');

		foreach ($projects as $project) {
			try {
				$teamLeader = $project->teamLeader;
				$this->info('Sending project due notification to team leader ' . $teamLeader->name);
				Log::channel('notifications')->info('Sending project due notification to team leader', [
						'project_id' => $project->id,
						'project_name' => $project->project_name,
						'team_leader_id' => $teamLeader->id,
						'team_leader_name' => $teamLeader->name,
						'team_leader_email' => $teamLeader->email
				]);

				$teamLeader->notify(new ProjectDueNotification($project, $this->projectRepository));

				foreach ($teamLeader->teams as $team) {
					if ($team->manager) {
						$this->info('Sending project due notification to manager ' . $team->manager->name);
						Log::channel('notifications')->info('Sending project due notification to manager', [
								'project_id' => $project->id,
								'project_name' => $project->project_name,
								'team_id' => $team->id,
								'team_name' => $team->name,
								'manager_id' => $team->manager->id,
								'manager_name' => $team->manager->name,
								'manager_email' => $team->manager->email
						]);

						$team->manager->notify(new ManagerProjectDueNotification($project, $this->projectRepository));
					} else {
						$this->comment('No manager found for team ' . $team->name);
						Log::channel('notifications')->info('No manager found for team', [
								'project_id' => $project->id,
								'team_id' => $team->id,
								'team_name' => $team->name
						]);
					}
				}

				if ($teamLeader->teams->isEmpty()) {
					$this->comment('Team leader does not belong to any teams');
					Log::channel('notifications')->info('Team leader does not belong to any teams', [
							'project_id' => $project->id,
							'team_leader_id' => $teamLeader->id,
							'team_leader_name' => $teamLeader->name
					]);
				}

			} catch (\Exception $e) {
				$this->error('Failed to send project due notification');
				Log::error('Failed to send project due notification', [
						'project_id' => $project->id,
						'project_name' => $project->name,
						'error' => $e->getMessage(),
						'trace' => $e->getTraceAsString()
				]);
			}
		}

		Log::channel('notifications')->info('Completed project due notifications process');
		$this->info('Project due notifications sent successfully!');
	}
}