<?php

namespace App\Services;

use App\Repositories\AssetRepository;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AssetExportService
{
	protected $assetRepository;

	public function __construct(AssetRepository $assetRepository)
	{
		$this->assetRepository = $assetRepository;
	}

	public function exportToExcel(Request $request): BinaryFileResponse
	{
		$assets = $this->getFilteredAssets($request);

		if ($assets->isEmpty()) {
			return $this->generateEmptyExcel();
		}

		return $this->generateExcel($assets);
	}

	private function getFilteredAssets(Request $request)
	{
		$query = \App\Models\Asset::query();

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
				  ->orWhere('serial_number', 'like', "%{$search}%");
			});
		}

		if ($request->filled('status')) {
			$query->where('status', $request->status);
		}

		if ($request->filled('asset_type_id')) {
			$query->where('asset_type_id', $request->asset_type_id);
		}

		if ($request->filled('project_id')) {
			$query->where('project_id', $request->project_id);
		}

		return $query->get();
	}

	private function generateExcel($assets): BinaryFileResponse
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$headers = [
			'A1' => 'Asset Name',
			'B1' => 'Serial Number',
			'C1' => 'Model',
			'D1' => 'Status',
			'E1' => 'Cost',
			'F1' => 'Location',
			'G1' => 'Purchase Date',
			'H1' => 'Warranty Date',
			'I1' => 'Asset Type',
		];

		foreach ($headers as $cell => $value) {
			$sheet->setCellValue($cell, $value);
		}

		$sheet->getStyle('A1:J1')->applyFromArray([
			'font' => ['bold' => true],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => ['rgb' => 'DDDDDD'],
			],
		]);

		$row = 2;
		foreach ($assets as $asset) {
			$sheet->setCellValue('A' . $row, $asset->name);
			$sheet->setCellValue('B' . $row, $asset->serial_number);
			$sheet->setCellValue('C' . $row, $asset->model);
			$sheet->setCellValue('D' . $row, $asset->status);
			$sheet->setCellValue('E' . $row, $asset->cost);
			$sheet->setCellValue('F' . $row, $asset->location);
			$sheet->setCellValue('G' . $row, optional($asset->purchase_date)->format('Y-m-d'));
			$sheet->setCellValue('H' . $row, optional($asset->warranty_date)->format('Y-m-d'));
			$sheet->setCellValue('I' . $row, optional($asset->assetType)->name);
			$row++;
		}

		foreach (range('A', 'J') as $column) {
			$sheet->getColumnDimension($column)->setAutoSize(true);
		}

		return $this->saveAndDownload($spreadsheet, 'assets_export');
	}

	private function generateEmptyExcel(): BinaryFileResponse
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'No assets available for the selected filters');
		$sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
		$sheet->mergeCells('A1:J1');

		return $this->saveAndDownload($spreadsheet, 'empty_assets_export');
	}

	private function saveAndDownload(Spreadsheet $spreadsheet, string $fileNamePrefix): BinaryFileResponse
	{
		$fileName = $fileNamePrefix . '_' . date('Y-m-d_H-i-s') . '.xlsx';
		$tempPath = storage_path('app/temp/' . $fileName);

		if (!file_exists(storage_path('app/temp'))) {
			mkdir(storage_path('app/temp'), 0755, true);
		}

		$writer = new Xlsx($spreadsheet);
		$writer->save($tempPath);

		return response()->download($tempPath, $fileName, [
			'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		])->deleteFileAfterSend(true);
	}
}