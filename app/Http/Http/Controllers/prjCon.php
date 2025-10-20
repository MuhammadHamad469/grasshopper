<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Project;
use App\Repositories\AssetRepositoryInterface;
use App\Repositories\InvoiceRepositoryInterface;
use App\Repositories\ClientRepositoryInterface;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\QuoteRepositoryInterface;
use App\Repositories\SmmeRepositoryInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class prjCon extends Controller
{
	protected ProjectRepositoryInterface $projectRepository;
	protected InvoiceRepositoryInterface $invoiceRepository;
	protected ClientRepositoryInterface $clientRepository;
	protected QuoteRepositoryInterface $quoteRepository;
	protected AssetRepositoryInterface $assetRepository;

	protected SmmeRepositoryInterface $smmeRepository;

	public function __construct(
			ProjectRepositoryInterface $projectRepository,
			InvoiceRepositoryInterface $invoiceRepository,
			ClientRepositoryInterface $clientRepository,
			QuoteRepositoryInterface $quoteRepository,
			AssetRepositoryInterface $assetRepository,
			SmmeRepositoryInterface $smmeRepository

	) {
		$this->middleware('auth');
		$this->projectRepository = $projectRepository;
		$this->invoiceRepository = $invoiceRepository;
		$this->clientRepository  = $clientRepository;
		$this->quoteRepository   = $quoteRepository;
		$this->assetRepository   = $assetRepository;
		$this->smmeRepository    = $smmeRepository;
	}

	public function index(): View
	{
		$user = Auth::user();

//		$user->assignPermission('can_access_finance');
		$projectTypes = ['innovation', 'training', 'vegetation-management', 'planning'];
		$statuses = [Project::STATUS_PLANNED, Project::STATUS_IN_PROGRESS, Project::STATUS_COMPLETED];

		$projectCounts = $this->getProjectCounts($projectTypes, $statuses);

		$threeMonthsInvoicedTotalData = $this->invoiceRepository->getLastThreeMonthsInvoicedTotal();
		$quotedData = $this->quoteRepository->getLastThreeMonthsQuotedTotal();

		$months = [];
		$threeMonthsInvoicedTotal = [];

		//Assets
		$inUseAssetsCount = $this->assetRepository->countAssetsByStatus(Asset::IN_USE);
		$availableAssetsCount = $this->assetRepository->countAssetsByStatus(Asset::AVAILABLE);
		$inServiceAssetsCount = $this->assetRepository->countAssetsByStatus(Asset::IN_SERVICE);

		for ($i = 2; $i >= 0; $i--) {
			$month = Carbon::now()->subMonths($i);
			$months[] = $month->format('M');
			$threeMonthsInvoicedTotal[] = $threeMonthsInvoicedTotalData[$month->month] ?? 0;
			$threeMonthsQuotedTotal[] = $quotedData[$month->month] ?? 0;
		}


		$recentProjects = [
				(object) [
						'name' => 'Project Alpha',
						'type' => 'Construction',
						'location' => 'Cape Town',
						'start_date' => now()->subDays(30),
						'deadline' => now()->addDays(60),
						'status' => 'in_progress',
						'completion_percentage' => 40,
				],
				(object) [
						'name' => 'Project Beta',
						'type' => 'Software Development',
						'location' => 'Johannesburg',
						'start_date' => now()->subDays(60),
						'deadline' => now()->addDays(30),
						'status' => 'completed',
						'completion_percentage' => 100,
				],
				(object) [
						'name' => 'Project Gamma',
						'type' => 'Research',
						'location' => 'Durban',
						'start_date' => now()->addDays(10),
						'deadline' => now()->addDays(90),
						'status' => 'planned',
						'completion_percentage' => 0,
				],
		];


		$timelineMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
		$plannedProjectsTimeline = [5, 3, 6, 4, 2, 1];
		$ongoingProjectsTimeline = [2, 4, 3, 5, 6, 7];
		$completedProjectsTimeline = [1, 2, 4, 3, 5, 6];

		$locationNames = ['Cape Town', 'Johannesburg', 'Durban', 'Pretoria', 'Bloemfontein'];
		$projectsByLocation = [10, 15, 8, 12, 5];

		$completionRateMonths = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
		$projectCompletionRate = [20, 35, 50, 65, 80, 95];

		$teamNames = ['Team Alpha', 'Team Beta', 'Team Gamma', 'Team Delta', 'Team Epsilon'];
		$teamPerformance = [75, 60, 85, 90, 70];

		$resourceAllocation = [40, 20, 15, 10, 15];

		$projectTypeDistribution = [12, 8, 15, 10, 5];



		$data = [

				'timelineMonths' => $timelineMonths,
			'PlannedProjectsTimeline' => $plannedProjectsTimeline,
			'ongoingProjectsTimeline' => $ongoingProjectsTimeline,
			'completedProjectsTimeline' => $completedProjectsTimeline,
			'locationNames' => $locationNames,
			'projectsByLocation' => $projectsByLocation,
			'completionRateMonths' => $completionRateMonths,
			'projectCompletionRate' => $projectCompletionRate,
			'teamNames' => $teamNames,
			'teamPerformance' => $teamPerformance,
			'resourceAllocation' => $resourceAllocation,
			'projectTypeDistribution' => $projectTypeDistribution,
				'plannedProjectsTimeline' => $plannedProjectsTimeline,



				'totalProjects' => $this->projectRepository->countProjects(),
				'projectVegCount' => $projectCounts['vegetation-management']['total'],
				'totalRevenueString' => $this->invoiceRepository->getOverallTotalString(),
				'threeMonthsActualBudgetTotal' => $this->projectRepository->getRecentMonthsActualBudget(),
				'totalStudents' => $this->projectRepository->countStudents(),
				'vehicleTargetKms' => $this->projectRepository->countTargetVehicleKms(),
				'vehicleActualKms' => $this->projectRepository->countActualVehicleKms(),
				'projectCounts' => $projectCounts,
				'PROJECT_STATUS_PLANNED' => Project::STATUS_PLANNED,
				'PROJECT_STATUS_IN_PROGRESS' => Project::STATUS_IN_PROGRESS,
				'PROJECT_STATUS_COMPLETED' => Project::STATUS_COMPLETED,
				'months' => $months,
				'threeMonthsInvoicedTotal' => $threeMonthsInvoicedTotal,
				'threeMonthsQuotedTotal' => $threeMonthsQuotedTotal,
			  'inUseAssetsCount' => $inUseAssetsCount,
				'availableAssetsCount' => $availableAssetsCount,
				'inServiceAssetsCount' => $inServiceAssetsCount,
				'smmeStatusCounts' =>  $this->smmeRepository->getSmmeCountByStatus(),
				'recentProjects' => $recentProjects,
		];

		return view('home', $data);
	}

	private function getProjectCounts(array $projectTypes, array $statuses): array
	{
		$counts = [];

		foreach ($projectTypes as $type) {
			$counts[$type] = ['total' => $this->projectRepository->countProjects(['project_type_id_slug' => $type])];
			foreach ($statuses as $status) {
				$counts[$type][$status] = $this->projectRepository->countProjects([
						'project_type_id_slug' => $type,
						'status' => $status
				]);
			}
		}

		return $counts;
	}
}