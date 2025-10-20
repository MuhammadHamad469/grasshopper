<?php

namespace App\Services;

use App\Models\ProjectType;
use App\Repositories\ProjectRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProjectExportService
{
	protected $projectRepository;

	public function __construct(ProjectRepository $projectRepository)
	{
		$this->projectRepository = $projectRepository;
	}

	/**
	 * Generate and download Excel file with filtered projects
	 *
	 * @param Request $request
	 * @return BinaryFileResponse
	 */
	public function exportToExcel(Request $request): BinaryFileResponse
	{
		$dateRange = getFinancialYearDates();
		$projects = $this->getFilteredProjects($request, $dateRange);

		if ($projects === null) {
			return $this->generateEmptyExcel();
		}

		return $this->generateExcel($projects);
	}

	/**
	 * Get filtered projects based on request parameters
	 *
	 * @param Request $request
	 * @param array $dateRange
	 * @return \Illuminate\Database\Eloquent\Collection|null
	 */
	private function getFilteredProjects(Request $request, array $dateRange)
	{
		$startDate = $dateRange['start'];
		$endDate = $dateRange['end'];

		// Initialize the query with filters
		$query = $this->projectRepository->query();

		// Apply search filter
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

		// Apply type filter
		if ($request->filled('type')) {
			$query->where('project_type_id', $request->type);
		}

		// Apply status filter
		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		// Apply team leader filter
		if ($request->filled('team_leader')) {
			$query->where('team_leader_user_id', $request->team_leader);
		}

		// Apply permission-based project type filtering
		$allowedProjectTypeIds = $this->getAllowedProjectTypeIds();

		// If no project types are allowed, return null
		if (empty($allowedProjectTypeIds)) {
			return null;
		}

		$query->whereIn('project_type_id', $allowedProjectTypeIds);

		// Get all projects without pagination
		$projects = $query->get();

		return $projects;
	}

	/**
	 * Get allowed project type IDs based on user permissions
	 *
	 * @return array
	 */
	private function getAllowedProjectTypeIds(): array
	{
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
			$allowedProjectTypeIds[] = ProjectType::where('name', 'SMME_Development')->value('id');
			$allowedProjectTypeIds[] = ProjectType::where('name', 'Other')->value('id');
		}

		return $allowedProjectTypeIds;
	}

	/**
	 * Generate Excel file from projects collection
	 *
	 * @param \Illuminate\Database\Eloquent\Collection $projects
	 * @return BinaryFileResponse
	 */
	private function generateExcel($projects): BinaryFileResponse
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// Set up headers
		$headers = [
			// 'A1' => 'Project ID',
			'B1' => 'Project Name',
			'C1' => 'Project Type',
			'D1' => 'Team Leader',
			'E1' => 'Status',
			'F1' => 'Working Days',
			// 'G1' => 'Daily Rate',
			// 'H1' => 'Total Expense',
			// 'I1' => 'Created At',
		];

		foreach ($headers as $cell => $value) {
			$sheet->setCellValue($cell, $value);
		}

		// Style headers
		$headerStyle = [
			'font' => [
				'bold' => true,
			],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => [
					'rgb' => 'DDDDDD',
				],
			],
		];
		$sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

		// Fill data
		$row = 2;

		foreach ($projects as $project) {
			// $sheet->setCellValue('A' . $row, $project->id);
			$sheet->setCellValue('B' . $row, $project->project_name);
			$sheet->setCellValue('C' . $row, $project->projectType->name ?? '');
			$sheet->setCellValue('D' . $row, $project->teamLeader->name ?? '');
			$sheet->setCellValue(
				'E' . $row,
				$project->status == 1 ? 'Planned' : ($project->status == 2 ? 'Ongoing' : 'Completed')
			);
			$sheet->setCellValue(
				'F' . $row,
				($project->startDate ? $project->startDate->format('Y-m-d') : '') .
					'to ' .
					($project->endDate ? $project->endDate->format('Y-m-d') : '')
			);
			// $sheet->setCellValue('G' . $row, $project->daily_rate);
			// $sheet->setCellValue('H' . $row, $project->total_expense);
			// $sheet->setCellValue('I' . $row, $project->created_at->format('Y-m-d'));
			$row++;
		}

		// Auto size columns
		foreach (range('A', 'I') as $column) {
			$sheet->getColumnDimension($column)->setAutoSize(true);
		}

		return $this->saveAndDownload($spreadsheet, 'projects_export');
	}

	/**
	 * Generate an empty Excel file when no projects are available
	 *
	 * @return BinaryFileResponse
	 */
	private function generateEmptyExcel(): BinaryFileResponse
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// Set up headers
		$sheet->setCellValue('A1', 'No projects available based on your permissions');

		// Style the message
		$style = [
			'font' => [
				'bold' => true,
				'size' => 14,
			],
		];
		$sheet->getStyle('A1')->applyFromArray($style);
		$sheet->mergeCells('A1:E1');

		return $this->saveAndDownload($spreadsheet, 'empty_projects_export');
	}

	/**
	 * Save spreadsheet to temporary file and return download response
	 *
	 * @param Spreadsheet $spreadsheet
	 * @param string $fileNamePrefix
	 * @return BinaryFileResponse
	 */
	private function saveAndDownload(Spreadsheet $spreadsheet, string $fileNamePrefix): BinaryFileResponse
	{
		// Create a temporary file
		$fileName = $fileNamePrefix . '_' . date('Y-m-d_H-i-s') . '.xlsx';
		$tempPath = storage_path('app/temp/' . $fileName);

		// Make sure the directory exists
		if (!file_exists(storage_path('app/temp'))) {
			mkdir(storage_path('app/temp'), 0755, true);
		}

		// Save the spreadsheet to the temporary file
		$writer = new Xlsx($spreadsheet);
		$writer->save($tempPath);

		// Return the file as a download
		return response()->download($tempPath, $fileName, [
			'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		])->deleteFileAfterSend(true);
	}
}
