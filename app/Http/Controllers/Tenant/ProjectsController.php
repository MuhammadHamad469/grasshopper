<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Project;
use App\Models\ProjectType;
use App\Models\User;
use App\Notifications\ProjectAllocationNotification;
use App\Services\ProjectExportService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Repositories\ProjectRepositoryInterface;
use App\Repositories\InvoiceRepositoryInterface;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\FileUploadService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class ProjectsController extends Controller
{
	protected ProjectRepositoryInterface $projectRepository;
	protected InvoiceRepositoryInterface $invoiceRepository;
	protected $fileUploadService;


	public function __construct(
		ProjectRepositoryInterface $projectRepository,
		InvoiceRepositoryInterface $invoiceRepository,
		FileUploadService $fileUploadService
	) {
		$this->middleware('auth');
		$this->projectRepository = $projectRepository;
		$this->invoiceRepository = $invoiceRepository;
		$this->fileUploadService = $fileUploadService;
		$this->moduleName = 'Project Management';

		view()->share('moduleName', $this->moduleName);
	}

	/**
	 * @return View
	 */
	// public function index(Request $request): View
	// {

	// 	$dateRange = getFinancialYearDates();
	// 	$startDate = $dateRange['start'];
	// 	$endDate = $dateRange['end'];

	// 	$query = $this->projectRepository->query();

	// 	// Apply search filter
	// 	if ($request->filled('search')) {
	// 		$search = $request->search;
	// 		$query->where(function ($q) use ($search) {
	// 			$q->where('project_name', 'like', "%{$search}%")
	// 				->orWhereHas('projectType', function ($q) use ($search) {
	// 					$q->where('name', 'like', "%{$search}%");
	// 				})
	// 				->orWhereHas('teamLeader', function ($q) use ($search) {
	// 					$q->where('name', 'like', "%{$search}%");
	// 				});
	// 		});
	// 	}

	// 	if ($request->filled('type')) {
	// 		$query->where('project_type_id', $request->type);
	// 	}

	// 	if ($request->filled('status')) {
	// 		$query->where('status', $request->status);
	// 	}

	// 	if ($request->filled('team_leader')) {
	// 		$query->where('team_leader_user_id', $request->team_leader);
	// 	}

	// 	$allowedProjectTypeIds = [];

	// 	if (Gate::allows('has_permission', 'can_access_veg')) {
	// 		$allowedProjectTypeIds[] = ProjectType::where('name', 'Vegetation Management')->value('id');
	// 	}
	// 	if (Gate::allows('has_permission', 'can_access_training')) {
	// 		$allowedProjectTypeIds[] = ProjectType::where('name', 'Training')->value('id');
	// 	}
	// 	if (Gate::allows('has_permission', 'can_access_innovation')) {
	// 		$allowedProjectTypeIds[] = ProjectType::where('name', 'Innovation')->value('id');
	// 	}
	// 	if (Gate::allows('has_permission', 'can_access_planning')) {
	// 		$allowedProjectTypeIds[] = ProjectType::where('name', 'Consulting')->value('id');
	// 	}
	// 	if (Gate::allows('has_permission', 'can_access_planning')) {
	// 		$allowedProjectTypeIds[] = ProjectType::where('name', 'SMME_Development')->value('id');
	// 	}
	// 	if (Gate::allows('has_permission', 'can_access_planning')) {
	// 		$allowedProjectTypeIds[] = ProjectType::where('name', 'Other')->value('id');
	// 	}

	// 	// If no project types are allowed, return empty collection
	// 	if (empty($allowedProjectTypeIds)) {
	// 		return view('tenant.projects.no-projects');
	// 	}

	// 	$query->whereIn('project_type_id', $allowedProjectTypeIds);

	// 	$projects = $query->paginate(10);
	// 	foreach ($projects as $project) {
	// 		$expensesData = $this->projectRepository->calculateProjectExpenses($project, $startDate, $endDate);
	// 		$project->working_days = $expensesData['working_days'];
	// 		$project->daily_rate = $expensesData['daily_rate'];
	// 		$project->total_expense = $expensesData['total_expense'];
	// 	}

	// 	// Get statistics
	// 	$totalProjects = $this->projectRepository->countProjects();
	// 	$projectVegCount = $this->projectRepository->countProjects(['project_type_id_slug' => 'vegetation-management']);
	// 	$totalRevenueString = $this->invoiceRepository->getOverallTotalString();
	// 	$vehicleTargetKms = $this->projectRepository->countTargetVehicleKms();
	// 	$vehicleActualKms = $this->projectRepository->countActualVehicleKms();

	// 	$projectTypes = ProjectType::all();
	// 	$teamLeaders = User::all();
	// 	return view('tenant.projects.index', compact(
	// 		'projects',
	// 		'totalProjects',
	// 		'projectVegCount',
	// 		'totalRevenueString',
	// 		'vehicleTargetKms',
	// 		'vehicleActualKms',
	// 		'projectTypes',
	// 		'teamLeaders',
	// 	));
	// }
	public function index(Request $request): View
{
    $dateRange = getFinancialYearDates();
    $startDate = $dateRange['start'];
    $endDate = $dateRange['end'];

    $query = $this->projectRepository->query();

    // 🔍 Search filter
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('project_name', 'like', "%{$search}%")
                ->orWhereHas('projectType', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('teamLeader', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
        });
    }

    // 📊 Other filters
    if ($request->filled('type')) {
        $query->where('project_type_id', $request->type);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('team_leader')) {
        $query->where('team_leader_user_id', $request->team_leader);
    }

    // ✅ Allowed project types
    $allowedProjectTypeIds = [];

    if (Gate::allows('has_permission', 'can_access_veg')) {
        $allowedProjectTypeIds[] = ProjectType::where('name', 'Vegetation Management')->value('id');
    }
    if (Gate::allows('has_permission', 'can_access_training')) {
        $allowedProjectTypeIds[] = ProjectType::where('name', 'Training')->value('id');
    }
    if (Gate::allows('has_permission', 'can_access_innovation')) {
        $allowedProjectTypeIds[] = ProjectType::where('name', 'Innovation')->value('id');
    }
    if (Gate::allows('has_permission', 'can_access_planning')) {
        $allowedProjectTypeIds[] = ProjectType::where('name', 'Consulting')->value('id');
    }

    // 🟡 Always include SMME Development (no permission needed)
    $allowedProjectTypeIds[] = ProjectType::where('name', 'SMME Development')->value('id');

    if (Gate::allows('has_permission', 'can_access_planning')) {
        $allowedProjectTypeIds[] = ProjectType::where('name', 'Other')->value('id');
    }

    // ⛔ If no project types are allowed
    if (empty(array_filter($allowedProjectTypeIds))) {
        return view('tenant.projects.no-projects');
    }

    // 📌 Apply filter by type
    $query->whereIn('project_type_id', $allowedProjectTypeIds);

    $projects = $query->paginate(10);

    // 💰 Calculate extra info
    foreach ($projects as $project) {
        $expensesData = $this->projectRepository->calculateProjectExpenses($project, $startDate, $endDate);
        $project->working_days = $expensesData['working_days'];
        $project->daily_rate = $expensesData['daily_rate'];
        $project->total_expense = $expensesData['total_expense'];
    }

    // 📊 Stats
    $totalProjects = $this->projectRepository->countProjects();
    $projectVegCount = $this->projectRepository->countProjects(['project_type_id_slug' => 'vegetation-management']);
    $totalRevenueString = $this->invoiceRepository->getOverallTotalString();
    $vehicleTargetKms = $this->projectRepository->countTargetVehicleKms();
    $vehicleActualKms = $this->projectRepository->countActualVehicleKms();

    // 📋 For filters
    $projectTypes = ProjectType::all();
    $teamLeaders = User::all();

    return view('tenant.projects.index', compact(
        'projects',
        'totalProjects',
        'projectVegCount',
        'totalRevenueString',
        'vehicleTargetKms',
        'vehicleActualKms',
        'projectTypes',
        'teamLeaders',
    ));
}




	public function create(): View
	{
		if (!Gate::allows('has_permission', 'can_create_projects')) {
			return view('errors.403');
		}
		$getAssets =  \App\Models\Asset::get();
		$data = $this->projectRepository->getCreateData();
		return view('tenant.projects.create', $data, compact('getAssets'));
	}

	public function show($id)
	{
		if (!Gate::allows('has_permission', 'can_view_projects')) {
			return view('errors.403');
		}

		$project = $this->projectRepository->findOrFail((int)$id);
		$teamLeader = $this->projectRepository->getTeamLeader($project);
		$smme = $this->projectRepository->getProjectSmme($project);


		$project->formatted_start_date = $project->startDate ? Carbon::parse($project->startDate)->format('M d, Y') : 'Not set';
		$project->formatted_end_date = $project->endDate ? Carbon::parse($project->endDate)->format('M d, Y') : 'Not set';

		$teamLeaderName = $teamLeader->name ?? 'Not Assigned';
		$progress = null;
		if ($project->projectType->id == 1) {
			$progress = $this->projectRepository->calculateProjectProgress($project);
			return view('tenant.projects.veg-show', compact('project', 'progress', 'teamLeaderName', 'smme'));
		}

		$progress = $this->projectRepository->calculateProjectProgress($project);

		if ($project->projectType && $project->projectType->id == 2) {
			return view('tenant.projects.train-show', compact('project', 'progress', 'teamLeaderName', 'smme'));
		}

		if ($project->projectType && $project->projectType->id == 3) {
			$progress = $this->projectRepository->calculateProjectProgress($project);
			return view('tenant.projects.inno-show', compact('project', 'progress', 'teamLeaderName', 'smme'));
		}

		if ($project->projectType && $project->projectType->id == 4) {
			$progress = $this->projectRepository->calculateProjectProgress($project);
			return view('tenant.projects.plan-show', compact('project', 'progress', 'teamLeaderName', 'smme'));
		}

		if ($project->projectType && $project->projectType->id == 5) {
			$progress = $this->projectRepository->calculateProjectProgress($project);
			return view('tenant.projects.smme-show', compact('project', 'progress', 'teamLeaderName', 'smme'));
		}

		if ($project->projectType && $project->projectType->id == 6) {
			$progress = $this->projectRepository->calculateProjectProgress($project);
			return view('tenant.projects.other-show', compact('project', 'progress', 'teamLeaderName', 'smme'));
		}


		return view('tenant.projects.show', compact('project', 'progress', 'teamLeaderName'));
	}

	public function edit($project)
	{
		if (!Gate::allows('has_permission', 'can_edit_projects')) {
			return view('errors.403');
		}
		$data = $this->projectRepository->getEditData($project);
		$getAssets =  \App\Models\Asset::get();
		// return $ge;
		return view('tenant.projects.edit', $data, compact('getAssets'));
	}

	public function store(ProjectRequest $request)
{
    // 📝 This was stopping all code below from executing — just comment it instead of removing.
    // return $request->all();

    // ✅ Validate input
    $validated = $request->validated();

    // 🗓 Convert dates to proper format
    $validated['startDate'] = date('Y-m-d', strtotime($request->startDate));
    $validated['endDate'] = date('Y-m-d', strtotime($request->endDate));

    // 🏗 Store project in DB
    $project = $this->projectRepository->store($validated);

    // 👤 Send notification to Team Leader (if exists)
    if ($project->teamLeader) {
        $project->teamLeader->notify(new ProjectAllocationNotification($project));
    }

    // 📁 Handle file uploads dynamically
    $uploadFields = $this->getUploadFieldsForProjectType($project->projectType->id);
    foreach ($uploadFields as $field) {
        if ($request->hasFile($field)) {
            $folder = 'projects/' . $this->getProjectTypeFolder($project->projectType->id);
            $filePath = $this->fileUploadService->upload($request->file($field), $folder);
            $project->$field = $filePath;
        }
    }

    // 💾 Save file paths to DB
    $project->save();

    // ✅ Redirect back with success message
    return redirect()->route('tenant.projects.index')->with('success', 'Project created successfully!');
}



	protected function getProjectTypeFolder($projectTypeId): string
	{
		
		switch ($projectTypeId) {
			case 1:
				return 'vegetation';
			case 2:
				return 'training';
			case 3:
				return 'innovation';
			case 4:
				return 'planning';
			case 5:
				return 'smme';
			case 6:
				return 'other';
			default:
				return 'general';
		}
	}


	protected function getUploadFieldsForProjectType($projectTypeId): array
	{
		// Define upload fields for each projectType
		switch ($projectTypeId) {
			case 1: // Vegetation
				return ['inspection_check', 'labour_report_check', 'safety_talk_check', 'herbicide_check', 'invoice_check'];
			case 2: // Training
				return ['experience_cv', 'facilitation_check', 'assessment_check', 'moderation_check', 'database_admin_check', 'certification_check'];
			case 3: // Innovation
				return ['proposal'];
			case 4: // Planning
				return ['project_plan'];
			case 5: // SMME
				return ['business_plan', 'financial_statement'];
			case 6: // Other
				return ['document'];
			default:
				return []; // No upload fields for unknown project types
		}
	}


	public function update(ProjectRequest $request, Project $project)
	{
		try {
			$oldTeamLeaderId = $project->teamLeader->id;

			$this->projectRepository->update($project, $request->validated());
			$project = $project->fresh();


			if ($project->teamLeader->id !== $oldTeamLeaderId && $project->teamLeader) {
				$project->teamLeader->notify(new ProjectAllocationNotification($project));

				Log::channel('notifications')->info('Team leader notification sent', [
					'project_id' => $project->id,
					'team_leader_id' => $project->teamLeader->id
				]);
			}
		} catch (\Exception $e) {
			Log::channel('notifications')->error('Failed to send team leader notification', [
				'project_id' => $project->id,
				'team_leader_id' => $project->teamLeader->id ?? 'Not Assigned',
				'error' => $e->getMessage()
			]);
			Log::error('Failed to send team leader notification', [
				'project_id' => $project->id,
				'team_leader_id' => $project->teamLeader->id ?? 'Not Assigned',
				'error' => $e->getMessage()
			]);
		}
		Log::info('Project updated successfully', [
			'project_id' => $project->id,
		]);


		// if ($request->hasFile('experience_cv') && $request->file('experience_cv')->isValid()) {
		// 	if ($project->experience_cv) {
		// 		Storage::delete($project->experience_cv);
		// 	}
		// 	$filePath = $this->fileUploadService->upload($request->file('experience_cv'));
		// 	$project->experience_cv = $filePath;
		// 	$project->save();
		// }

		$uploadFields = $this->getUploadFieldsForProjectType($project->projectType->id);

		foreach ($uploadFields as $field) {
			if ($request->hasFile($field) && $request->file($field)->isValid()) {
				// Delete old file if exists
				if ($project->$field) {
					Storage::delete($project->$field);
				}
				// Determine upload folder based on project type
				$folder = 'projects/' . $this->getProjectTypeFolder($project->projectType->id);

				$filePath = $this->fileUploadService->upload($request->file($field), $folder);

				$project->$field = $filePath;
			}
		}
		$project->save();

		return redirect()->route('tenant.projects.show', $project)
			->with('success', 'Project updated successfully.');
	}

	public function destroy(Project $project)
	{
		$this->projectRepository->delete($project);
		return redirect()->route('tenant.projects.index')->with('success', 'Project deleted successfully.');
	}

	public function exportToExcel(Request $request, ProjectExportService $exportService): BinaryFileResponse
	{

		return $exportService->exportToExcel($request);
	}


	public function countTargetVehicleKms1(array $filters = []): int
	{
		$query = Project::query();

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$start = Carbon::parse($filters['start_date'])->startOfDay();
			$end   = Carbon::parse($filters['end_date'])->endOfDay();

			$query->whereBetween('created_at', [$start, $end]);
		}

		return (int) $query->sum('vehicle_kms_target');
	}

	public function countActualVehicleKms1(array $filters = []): int
	{
		$query = Project::query();

		if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
			$start = Carbon::parse($filters['start_date'])->startOfDay();
			$end   = Carbon::parse($filters['end_date'])->endOfDay();

			$query->whereBetween('created_at', [$start, $end]);
		}

		return (int) $query->sum('actual_vehicle_kms');
	}


	//	public function countTargetVehicleKms1(array $filters = []): int
	//	{
	//		return Project::when(
	//			isset($filters['start_date'], $filters['end_date']),
	//			fn($q) => $q->whereBetween('created_at', [
	//				$filters['start_date'],
	//				$filters['end_date']
	//			])
	//		)->sum('vehicle_kms_target');
	//	}
	//
	//	public function countActualVehicleKms1(array $filters = []): int
	//	{
	//		return Project::when(
	//			isset($filters['start_date'], $filters['end_date']),
	//			fn($q) => $q->whereBetween('created_at', [
	//				$filters['start_date'],
	//				$filters['end_date']
	//			])
	//		)->sum('actual_vehicle_kms');
	//	}


	public function getProjectCompletionRate(?Carbon $startDate = null, ?Carbon $endDate = null): array
	{
		$start = $startDate ? Carbon::parse($startDate)->startOfDay() : Carbon::now()->subMonths(6)->startOfDay();
		$end = $endDate ? Carbon::parse($endDate)->endOfDay() : Carbon::now()->endOfDay();
		$rows = Project::whereBetween('created_at', [$start, $end])
			->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
			->groupBy('month')
			->orderBy('month')
			->get();
		return [
			'months' => $rows->pluck('month')->toArray(),
			'rates'  => $rows->pluck('total')->toArray(),
		];
	}

	/**
	 * Display the project management dashboard.
	 *
	 * @return View
	 */
	public function dashboard(Request $request): View
	{
		$request->validate([
			'start_date' => 'nullable|date',
			'end_date'   => 'nullable|date|after_or_equal:start_date',
		], [
			'end_date.after_or_equal' => 'The end date must be a date after or equal to start date.',
		]);


		$startDate = $request->input('start_date');
		$endDate = $request->input('end_date');

		if ($startDate && $endDate) {
			$startDate = Carbon::parse($startDate);
			$endDate = Carbon::parse($endDate);
		} else {
			$dateRange = getFinancialYearDates();
			$startDate = $dateRange['start'];
			$endDate = $dateRange['end'];
		}
		$query = $this->projectRepository->query();
		$projects = $query->with(['teamLeader.employee'])->get();
		$getprojects = $query->with(['teamLeader.employee'])
			->whereBetween('created_at', [$startDate, $endDate])
			->get();
		$projectNames = [];
		$projectBudgets = [];
		$projectExpenses = [];
		foreach ($getprojects as $project) {
			$projectNames[] = $project->project_name;
			$projectBudgets[] = $project->budget;
			$expenseData = $this->projectRepository->calculateProjectExpenses($project, $startDate, $endDate);
			$projectExpenses[] = $expenseData['total_expense'];
		}

		$timeLabels = $projects->pluck('created_at')->map(function ($date) {
			return $date->format('M'); // Format as needed
		});

		$vehicleKmsTargetData = $projects->pluck('vehicle_kms_target');
		$actualVehicleKmsData = $projects->pluck('actual_vehicle_kms');
		//		dd($vehicleKmsTargetData, $actualVehicleKmsData);

		$projectTypes = ['innovation', 'training', 'vegetation-management', 'Consulting', 'SMME_Development', 'Other'];
		$statuses = [Project::STATUS_PLANNED, Project::STATUS_IN_PROGRESS, Project::STATUS_COMPLETED];
		$projectCounts = $this->getProjectCounts($projectTypes, $statuses, $startDate, $endDate);

		$activeProjects = $this->projectRepository->countProjects(['status' => Project::STATUS_IN_PROGRESS], $startDate, $endDate);
		$plannedProjects = $this->projectRepository->countProjects(['status' => Project::STATUS_PLANNED], $startDate, $endDate);
		$completedProjects = $this->projectRepository->countProjects(['status' => Project::STATUS_COMPLETED], $startDate, $endDate);
		$totalProjects = $this->projectRepository->countProjects([], $startDate, $endDate);

		// Timeline and project distribution data
		$timelineData = $this->projectRepository->getProjectsTimeline($startDate, $endDate);
		$locationData = $this->projectRepository->getProjectsByLocation($startDate, $endDate);

		$completionRateData = $this->getProjectCompletionRate($startDate, $endDate);
		$teamPerformanceData = [75, 60, 85, 90, 70];; //$this->projectRepository->getTeamPerformance();
		$resourceAllocationData = [75, 60, 85, 90, 70]; //$this->projectRepository->getResourceAllocation();
		$projectTypeDistributionData = $this->projectRepository->getProjectTypeDistribution($startDate, $endDate);
		//    dd($projectTypeDistributionData);
		$recentProjects = $this->projectRepository->getRecentProjects(5);

		// $compProject = Project::all();
		// print_r($compProject->toArray());
		// dd($timelineData);
		// die();
		return view('tenant.projects.dashboard', [
			'totalProjects'             => $totalProjects,
			'activeProjects'            => $activeProjects,
			'totalVegProjects'          => $projectCounts['vegetation-management']['total'],
			'plannedProjects'           => $plannedProjects,
			'completedProjects'         => $completedProjects,
			'timelineMonths'            => $timelineData['months'] ?? [],
			'plannedProjectsTimeline'   => $timelineData['planned'] ?? [],
			'ongoingProjectsTimeline'   => $timelineData['ongoing'] ?? [],
			'completedProjectsTimeline' => $timelineData['completed'] ?? [],
			'locationNames'             => $locationData['locationNames'] ?? [],
			'projectsByLocation'        => $locationData['projectsByLocation'] ?? [],
			'completionRateMonths'      => $completionRateData['months'] ?? [],
			'projectCompletionRate'     => $completionRateData['rates'] ?? [],
			'resourceAllocation'        => $resourceAllocationData['allocation'] ?? [],
			'projectTypeDistribution'   => $projectTypeDistributionData,
			'teamNames'                 =>  ['Operations', 'Training', 'Innovation', 'Vegitation', 'Planning'],
			'teamPerformance'           => [75, 60, 85, 90, 70],
			'recentProjects'            => $recentProjects,
			'projectNames'              => json_encode($projectNames),
			'projectBudgets'            => json_encode($projectBudgets),
			'projectExpenses'           => json_encode($projectExpenses),
			'vehicleTargetKms' => $this->countTargetVehicleKms1([
				'start_date' => $startDate,
				'end_date'   => $endDate,
			]),
			'vehicleActualKms' => $this->countActualVehicleKms1([
				'start_date' => $startDate,
				'end_date'   => $endDate,
			]),


		]);
	}



	private function getProjectCounts(array $projectTypes, array $statuses, $startDate, $endDate): array //TODO rename and move to REPO
	{
		$counts = [];

		foreach ($projectTypes as $type) {
			$counts[$type] = ['total' => $this->projectRepository->countProjects(['project_type_id_slug' => $type], $startDate, $endDate)];
			foreach ($statuses as $status) {
				$counts[$type][$status] = $this->projectRepository->countProjects([
					'project_type_id_slug' => $type,
					'status' => $status
				], $startDate, $endDate);
			}
		}

		return $counts;
	}
}
