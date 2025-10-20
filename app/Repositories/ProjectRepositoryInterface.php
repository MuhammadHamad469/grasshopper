<?php

namespace App\Repositories;

use App\Models\Project;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

interface ProjectRepositoryInterface
{
	public function getPaginatedProjects(int $perPage, ?Carbon $startDate = null, ?Carbon $endDate = null): LengthAwarePaginator;


	public function getCreateData(): array;

	public function findOrFail(int $id): Project;

	public function getEditData($project): array;

	public function store(array $data): Project;

	public function update(Project $project, array $data): Project;

	public function delete(Project $project): bool;

	public function syncAssets(Project $project, array $assetIds): void;

	public function getTeamLeader(Project $project): ?User;

	public function getProjectSmme(Project $project);

	public function calculateProjectProgress($project): array;

	public function countProjects(array $filters = []): int;

	public function countStudents(array $filters = []): int;

	public function countTargetVehicleKms(array $filters = []): int;

	public function countActualVehicleKms(array $filters = []): int;

	public function getRecentMonthsActualBudget(int $numberOfMonths = 3): array;

	public function getRecentProjects(int $limit = 3): array;

	public function getThreeMonthsInvoicedAndQuotedTotals(): array;

	public function getProjectsTimeline(): array;

	public function getProjectsByLocation(): array;

	public function getProjectCompletionRate(int $months = 6): array;

	public function getTotalUserProjects(?int $teamLeaderId = null): int;

	public function getProjectTypeDistribution(): array;

	public function getTotalCompletedProjects(?int $teamLeaderId = null): int;

	public function getUserNextDeadline(?int $teamLeaderId = null): ?Carbon;
	public function calculateProjectExpenses(Project $project, ?Carbon $startDate = null, ?Carbon $endDate = null): array;
	public function getSubordinatesProjects(User $user);
}
