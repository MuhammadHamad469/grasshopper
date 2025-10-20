<?php

namespace App\Repositories;

use App\Models\Asset;
use App\Models\Location;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\Quote;
use App\Models\Role;
use App\Models\Smmes;
use App\Models\User;
use App\Services\DateService;
use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class ProjectRepository implements ProjectRepositoryInterface
{
	protected DateService $dateService;
	public function __construct(DateService $dateService)
	{
		$this->dateService = $dateService;
	}


	/**
	 * Get paginated projects with optional date range filter
	 *
	 * @param int $perPage
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return LengthAwarePaginator
	 */
	public function getPaginatedProjects(int $perPage, ?Carbon $startDate = null, ?Carbon $endDate = null): LengthAwarePaginator
	{
		$query = Project::with('smme');

		// Apply date range filter if provided, otherwise use financial year
		$this->applyDateRangeFilter($query, $startDate, $endDate);

		return $query->paginate($perPage);
	}

	public function getCreateData(): array
	{
		return [
			'assets' => Asset::where('status', Asset::AVAILABLE)->get(),
			'projectTypes' => ProjectType::all(),
			'quotes' => Quote::all(),
			'teamLeaders' => User::all(),
			'locations' => Location::all(),
			'smmes' => Smmes::all(),
		];
	}

	public function findOrFail(int $id): Project
	{
		return Project::findOrFail($id);
	}

	public function getEditData($project): array
	{
		$project = $this->findOrFail($project);
		return array_merge($this->getCreateData(), [
			'project' => $project,
			'assets' => Asset::where('project_id', $project->id)
				->orWhere('status', Asset::AVAILABLE)
				->get(),
			'selectedAssets' => $project->assets->pluck('id')->toArray(),
		]);
	}

	public function store(array $data): Project
	{
		return DB::transaction(function () use ($data) {
			$project = Project::create($data);
			$this->syncAssets($project, $data['assets'] ?? []);
			return $project;
		});
	}

	public function update(Project $project, array $data): Project
	{
		return DB::transaction(function () use ($project, $data) {
			$project->update($data);
			$this->syncAssets($project, $data['assets'] ?? []);
			return $project;
		});
	}

	public function delete(Project $project): bool
	{
		$this->detachAssets($project->assets);
		return $project->delete();
	}

	public function syncAssets(Project $project, array $assetIds): void
	{
		$assetsToDetach = $project->assets()->whereNotIn('id', $assetIds)->get();
		$this->detachAssets($assetsToDetach);
		$this->attachAssets($assetIds, $project);
	}

	private function detachAssets($assets): void
	{
		$assets->each(function ($asset) {
			$asset->update([
				'project_id' => null,
				'status' => Asset::AVAILABLE
			]);
		});
	}

	private function attachAssets(array $assetIds, Project $project): void
	{
		Asset::whereIn('id', $assetIds)->update([
			'project_id' => $project->id,
			'status' => Asset::IN_USE
		]);
	}

	public function getTeamLeader(Project $project): ?User
	{
		return $project->team_leader_user_id ? User::find($project->team_leader_user_id) : null;
	}

	public function getProjectSmme(Project $project)
	{
		return $project->relationLoaded('smme') ? $project->smme : $project->smme()->first();
	}

	public function calculateProjectProgress($project): array
{
    if (!$project instanceof Project) {
        $project = $this->findOrFail($project);
    }

    $startDate = new DateTime($project->startDate);
    $endDate = new DateTime($project->endDate);
    $currentDate = new DateTime();

    $totalDays = max($endDate->diff($startDate)->days + 1, 1); // prevent division by zero
    $currentDay = max($currentDate->diff($startDate)->days + 1, 1);

    // Prevent division by zero if target_hectares is null or 0
    $targetHectares = $project->target_hectares > 0 ? $project->target_hectares : 1;

    $targetPerDay = $targetHectares / $totalDays;
    $expectedProgress = $currentDay * $targetPerDay;

    $actualProgressPercentage = null;
    $expectedProgressPercentage = null;
    $differenceFromTarget = 0;

    if ($project->projectType && $project->projectType->id == 1) {
        $actualProgressPercentage = ($project->actual_hectares / $targetHectares) * 100;
        $expectedProgressPercentage = ($expectedProgress / $targetHectares) * 100;
        $differenceFromTarget = $actualProgressPercentage - $expectedProgressPercentage;
    }

    return [
        'totalDays' => $totalDays,
        'currentDay' => $currentDay,
        'targetPerDay' => $targetPerDay,
        'expectedProgress' => $expectedProgress,
        'actualProgressPercentage' => $actualProgressPercentage,
        'expectedProgressPercentage' => $expectedProgressPercentage,
        'differenceFromTarget' => $differenceFromTarget,
        'status' => $this->getProjectStatus($differenceFromTarget),
    ];
}


	private function getProjectStatus(float $differenceFromTarget): string
	{
		if ($differenceFromTarget < 0) {
			return 'Behind schedule';
		} elseif ($differenceFromTarget == 0) {
			return 'ON schedule';
		} else {
			return 'Ahead of schedule';
		}
	}

	/**
	 * Count projects with optional date range filter
	 *
	 * @param array $filters
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return int
	 */
	public function countProjects(array $filters = [], ?Carbon $startDate = null, ?Carbon $endDate = null): int
	{
		$query = $this->applyFilters($filters);
		$this->applyDateRangeFilter($query, $startDate, $endDate);
		$query->whereNull('deleted_at');
		return $query->count();
	}

	/**
	 * Count students with optional date range filter
	 *
	 * @param array $filters
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return int
	 */
	public function countStudents(array $filters = [], ?Carbon $startDate = null, ?Carbon $endDate = null): int
	{
		$query = $this->applyFilters($filters);
		$this->applyDateRangeFilter($query, $startDate, $endDate);

		$result = $query->sum('number_of_students');
		Log::info($query->toSql() . " " . $result);
		return $result;
	}

	/**
	 * Count target vehicle kms with optional date range filter
	 *
	 * @param array $filters
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return int
	 */
	public function countTargetVehicleKms(array $filters = [], ?Carbon $startDate = null, ?Carbon $endDate = null): int
	{
		$query = $this->applyFilters($filters);
		$this->applyDateRangeFilter($query, $startDate, $endDate);

		return $query->sum('vehicle_kms_target');
	}

	/**
	 * Count actual vehicle kms with optional date range filter
	 *
	 * @param array $filters
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return int
	 */
	public function countActualVehicleKms(array $filters = [], ?Carbon $startDate = null, ?Carbon $endDate = null): int
	{
		$query = $this->applyFilters($filters);
		$this->applyDateRangeFilter($query, $startDate, $endDate);

		return $query->sum('actual_vehicle_kms');
	}

	/**
	 * Get recent months actual budget with optional date range filter
	 *
	 * @param int $numberOfMonths
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return array
	 */
	public function getRecentMonthsActualBudget(int $numberOfMonths = 3, ?Carbon $startDate = null, ?Carbon $endDate = null): array
	{
		$query = $this->query()
			->select(
				DB::raw('MONTH(created_at) as month'),
				DB::raw('SUM(actual_budget) as total')
			);

		// Use provided date range or default to last N months
		if ($startDate && $endDate) {
			$query->whereBetween('created_at', [$startDate, $endDate]);
		} else {
			$query->where('created_at', '>=', Carbon::now()->subMonths($numberOfMonths));
		}

		$result = $query->groupBy('month')
			->orderBy('month', 'DESC')
			->get()
			->pluck('total', 'month')
			->toArray();

		// Determine the months to display based on input parameters
		if ($startDate && $endDate) {
			$startMonth = Carbon::parse($startDate);
			$endMonth = Carbon::parse($endDate);
			$monthDiff = $startMonth->diffInMonths($endMonth) + 1;
			$monthsToDisplay = min($monthDiff, $numberOfMonths);
		} else {
			$monthsToDisplay = $numberOfMonths;
		}

		$budgetData = [];
		for ($i = $monthsToDisplay - 1; $i >= 0; $i--) {
			$date = Carbon::now()->subMonths($i);
			$monthName = $date->format('M');
			$monthNumber = $date->month;
			$budgetData[$monthName] = $result[$monthNumber] ?? 0;
		}

		return $budgetData;
	}

	/**
	 * Get recent projects with optional date range filter
	 *
	 * @param int $limit
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return array
	 */
	public function getRecentProjects(int $limit = 3, ?Carbon $startDate = null, ?Carbon $endDate = null): array
	{
		$query = Project::with(['projectType', 'location']);

		// Apply date range filter if provided
		$this->applyDateRangeFilter($query, $startDate, $endDate);

		$projects = $query->orderBy('startDate', 'desc')
			->take($limit)
			->get();

		$recentProjects = [];
		foreach ($projects as $project) {
			$recentProjects[] = (object)[
				'name' => $project->project_name,
				'type' => $project->projectType->name ?? 'N/A',
				'location' => $project->location->name ?? 'N/A',
				'start_date' => $project->startDate,
				'endDate' => $project->endDate,
				'status' => $this->mapProjectStatus($project->status),
				'completion_percentage' => $this->calculateCompletionPercentage($project),
			];
		}

		return $recentProjects;
	}

	/**
	 * Get three months invoiced and quoted totals with optional date range filter
	 *
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return array
	 */
	public function getThreeMonthsInvoicedAndQuotedTotals(?Carbon $startDate = null, ?Carbon $endDate = null): array
	{
		// Use provided date range or default to financial year
		if (!$startDate || !$endDate) {
			$dateRange = getFinancialYearDates();
			$startDate = $dateRange['start'];
			$endDate = $dateRange['end'];
		}

		// Use the last 3 months within the provided date range
		$endMonth = Carbon::parse($endDate);
		$invoicedQuery = Project::whereBetween('created_at', [$startDate, $endDate])
			->selectRaw('MONTH(created_at) as month, SUM(actual_budget) as total')
			->groupBy('month');

		$quotedQuery = Quote::whereBetween('created_at', [$startDate, $endDate])
			->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
			->groupBy('month');

		$invoicedData = $invoicedQuery->pluck('total', 'month')->toArray();
		$quotedData = $quotedQuery->pluck('total', 'month')->toArray();

		// Get the last 3 months within the date range
		$threeMonthsInvoicedTotal = [];
		$threeMonthsQuotedTotal = [];

		// Get the last 3 months in the date range
		$monthsToInclude = min(3, $startDate->diffInMonths($endDate) + 1);
		for ($i = $monthsToInclude - 1; $i >= 0; $i--) {
			$month = Carbon::parse($endDate)->subMonths($i);
			$monthNumber = $month->month;
			$threeMonthsInvoicedTotal[] = $invoicedData[$monthNumber] ?? 0;
			$threeMonthsQuotedTotal[] = $quotedData[$monthNumber] ?? 0;
		}

		return [
			'threeMonthsInvoicedTotal' => $threeMonthsInvoicedTotal,
			'threeMonthsQuotedTotal' => $threeMonthsQuotedTotal,
		];
	}

	/**
	 * Get projects timeline with optional date range filter
	 *
	 * @param int $months
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return array
	 */
	public function getProjectsTimeline(?Carbon $startDate = null, ?Carbon $endDate = null): array
	{
		$months = 6;
		if ($startDate && $endDate) {
			$start = $startDate->copy()->startOfDay();
			$end = $endDate->copy()->endOfDay();
			$months = $start->diffInMonths($end) + 1;
		} else {
			$start = Carbon::now()->subMonths($months - 1)->startOfDay();
			$end = Carbon::now()->endOfDay();
		}

		$monthCount = $start->diffInMonths($end) + 1;
		$monthsToDisplay = min($months, $monthCount);

		$timelineMonths = [];
		$planned = [];
		$ongoing = [];
		$completed = [];

		for ($i = 0; $i < $monthsToDisplay; $i++) {
			$currentMonthStart = $start->copy()->addMonths($i)->startOfMonth();
			$currentMonthEnd = $start->copy()->addMonths($i)->endOfMonth();

			if ($i === 0) {
				$currentMonthStart = $start;
			}
			if ($i === $monthsToDisplay - 1) {
				$currentMonthEnd = $end;
			}

			$monthKey = $currentMonthStart->format('M Y');
			$timelineMonths[] = $monthKey;

			$planned[] = Project::where('status', Project::STATUS_PLANNED)
				->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
				->count();

			$ongoing[] = Project::where('status', Project::STATUS_IN_PROGRESS)
				->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
				->count();

			$completed[] = Project::where('status', Project::STATUS_COMPLETED)
				->whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
				->count();
		}

		return [
			'months' => $timelineMonths,
			'planned' => $planned,
			'ongoing' => $ongoing,
			'completed' => $completed,
			'start_date' => $start,
			'end_date' => $end,
		];
	}



	/**
	 * Get projects by location with optional date range filter
	 *
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return array
	 */
	public function getProjectsByLocation(?Carbon $startDate = null, ?Carbon $endDate = null): array
	{
		if (!$startDate || !$endDate) {
			$dateRange = getFinancialYearDates();
			$startDate = $dateRange['start'];
			$endDate = $dateRange['end'];
		}

		$locations = Location::withCount(['projects' => function ($query) use ($startDate, $endDate) {
			$query->whereBetween('created_at', [$startDate, $endDate]);
		}])->orderBy('projects_count', 'desc')->get();

		$locationNames = [];
		$projectsByLocation = [];
		foreach ($locations as $location) {
			$locationNames[] = $location->name;
			$projectsByLocation[] = $location->projects_count;
		}

		return [
			'locationNames' => $locationNames,
			'projectsByLocation' => $projectsByLocation,
		];
	}

	public function calculateProjectExpenses(Project $project, ?Carbon $startDate = null, ?Carbon $endDate = null): array
	{
		// If no dates provided, use project's dates
		if (!$startDate || !$endDate) {
			$startDate = $project->startDate;
			$endDate = $project->endDate;
		}

		// Get the team leader
		$teamLeader = $project->teamLeader;

		if (!$teamLeader || !$teamLeader->employee || !$teamLeader->employee->daily_rate) {
			return [
				'working_days' => 0,
				'daily_rate' => 0,
				'total_expense' => 0,
			];
		}

		$dailyRate = $teamLeader->employee->daily_rate;

		// Calculate working days between start and end date
		$workingDays = $this->dateService->calculateWorkingDays($startDate, $endDate);
		// Calculate total expense
		$totalExpense = $workingDays * $dailyRate;

		return [
			'working_days' => $workingDays,
			'daily_rate' => $dailyRate,
			'total_expense' => $totalExpense,
		];
	}

	/**
	 * Get project completion rate with optional date range filter
	 *
	 * @param int $months
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return array
	 */
	public function getProjectCompletionRate(int $months = 6, ?Carbon $startDate = null, ?Carbon $endDate = null): array
	{
		// Use provided date range or default to last N months
		if ($startDate && $endDate) {
			$start = Carbon::parse($startDate);
			$end = Carbon::parse($endDate);
		} else {
			$start = Carbon::now()->subMonths($months - 1);
			$end = Carbon::now();
		}

		// Calculate number of months in range
		$monthCount = $start->diffInMonths($end) + 1;
		$monthsToDisplay = min($months, $monthCount);

		$completionRateMonths = [];
		$completionRates = [];

		for ($i = 0; $i < $monthsToDisplay; $i++) {
			$month = clone $start;
			$month->addMonths($i);
			$monthKey = $month->format('M');
			$completionRateMonths[] = $monthKey;

			$projects = Project::whereYear('created_at', $month->year)
				->whereMonth('created_at', $month->month)
				->get();

			$totalCompletion = 0;
			$count = 0;
			foreach ($projects as $project) {
				$totalCompletion += $this->calculateCompletionPercentage($project);
				$count++;
			}

			$completionRates[] = $count > 0 ? round($totalCompletion / $count) : 0;
		}

		return [
			'months' => $completionRateMonths,
			'rates' => $completionRates,
		];
	}

	/**
	 * Get project type distribution with optional date range filter
	 *
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return array
	 */
	public function getProjectTypeDistribution(?Carbon $startDate = null, ?Carbon $endDate = null): array
	{
		// Use provided date range or default to financial year
		if (!$startDate || !$endDate) {
			$dateRange = getFinancialYearDates();
			$startDate = $dateRange['start'];
			$endDate = $dateRange['end'];
		}

		return ProjectType::withCount(['projects' => function ($query) use ($startDate, $endDate) {
			$query->whereBetween('created_at', [$startDate, $endDate]);
		}])
			->get()
			->pluck('projects_count')
			->toArray();
	}

	private function mapProjectStatus(int $status): string
	{
		$statuses = [
			Project::STATUS_PLANNED => 'planned',
			Project::STATUS_IN_PROGRESS => 'in_progress',
			Project::STATUS_COMPLETED => 'completed',
		];

		return $statuses[$status] ?? 'unknown';
	}

	private function calculateCompletionPercentage(Project $project): float
	{
		if ($project->projectType->id == 1 && $project->target_hectares > 0) {
			return ($project->actual_hectares / $project->target_hectares) * 100;
		}

		if ($project->status === Project::STATUS_COMPLETED) {
			return 100.0;
		}

		$start = Carbon::parse($project->startDate);
		$end = Carbon::parse($project->endDate);
		$now = Carbon::now();

		if ($now < $start) return 0.0;
		if ($now > $end) return 100.0;

		$totalDays = $start->diffInDays($end);
		$elapsedDays = $start->diffInDays($now);
		return round(($elapsedDays / $totalDays) * 100, 2);
	}

	/**
	 * Get total user projects with optional date range filter
	 *
	 * @param ?int $teamLeaderId
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return int
	 */
	public function getTotalUserProjects(?int $teamLeaderId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): int
	{
		$teamLeaderId = $teamLeaderId ?? auth()->id();

		$query = Project::where('team_leader_user_id', $teamLeaderId);
		$this->applyDateRangeFilter($query, $startDate, $endDate);

		return $query->count();
	}

	/**
	 * Get subordinates projects with optional date range filter
	 *
	 * @param User $user
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return \Illuminate\Support\Collection
	 */
	public function getSubordinatesProjects(User $user, ?Carbon $startDate = null, ?Carbon $endDate = null)
	{
		$subordinates = $user->getSubordinates();
		$query = Project::whereIn('team_leader_user_id', $subordinates->pluck('id'))
			->where('team_leader_user_id', '!=', $user->id);

		$this->applyDateRangeFilter($query, $startDate, $endDate);

		return $query->get()->groupBy('team_leader_user_id');
	}

	/**
	 * Get total completed projects with optional date range filter
	 *
	 * @param ?int $teamLeaderId
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return int
	 */
	public function getTotalCompletedProjects(?int $teamLeaderId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): int
	{
		// Use authenticated user if no team leader ID is provided
		$teamLeaderId = $teamLeaderId ?? auth()->id();

		$query = Project::where('team_leader_user_id', $teamLeaderId)
			->where('status', Project::STATUS_COMPLETED);

		$this->applyDateRangeFilter($query, $startDate, $endDate);

		return $query->count();
	}

	/**
	 * Get user next deadline with optional date range filter
	 *
	 * @param ?int $teamLeaderId
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return ?Carbon
	 */
	public function getUserNextDeadline(?int $teamLeaderId = null, ?Carbon $startDate = null, ?Carbon $endDate = null): ?Carbon
	{
		$teamLeaderId = $teamLeaderId ?? auth()->id();

		// Find the nearest upcoming endDate for projects led by this team leader
		$query = Project::where('team_leader_user_id', $teamLeaderId)
			->whereNotIn('status', [Project::STATUS_COMPLETED, Project::STATUS_PLANNED])
			->whereNotNull('endDate')
			->where('endDate', '>=', now());

		if ($startDate && $endDate) {
			$query->whereBetween('created_at', [$startDate, $endDate]);
		}

		return $query->orderBy('endDate', 'asc')->value('endDate');
	}

	public function query(bool $applyFinancialYear = true): Builder
	{
		$query = Project::query();

		// if ($applyFinancialYear) {
		// 	$dateRange = getFinancialYearDates();
		// 	$query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
		// }

		return $query;
	}

	private function applyFilters(array $filters = []): Builder
	{
		$query = $this->query();

		if (isset($filters['project_type_id_slug'])) {
			$slug = $filters['project_type_id_slug'];
			$query->whereHas('projectType', function ($q) use ($slug) {
				$q->where('slug', $slug);
			});
			unset($filters['project_type_id_slug']);
		}

		foreach ($filters as $field => $value) {
			if (is_array($value)) {
				$operator = $value[0] ?? '=';
				$filterValue = $value[1] ?? null;
				$query->where($field, $operator, $filterValue);
			} else {
				$query->where($field, $value);
			}
		}

		return $query;
	}

	/**
	 * Apply date range filter to a query
	 *
	 * @param Builder $query
	 * @param ?Carbon $startDate
	 * @param ?Carbon $endDate
	 * @return Builder
	 */
	private function applyDateRangeFilter(Builder $query, ?Carbon $startDate = null, ?Carbon $endDate = null): Builder
	{
		if ($startDate && $endDate) {
			return $query->whereBetween('created_at', [$startDate, $endDate]);
		}

		$dateRange = getFinancialYearDates();
		return $query->whereBetween('created_at', [$dateRange['start'], $dateRange['end']]);
	}
}
