<?php

namespace App\Services;

use App\Repositories\SmmeRepository;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SmmeExportService
{
	protected $smmeRepository;

	public function __construct(SmmeRepository $smmeRepository)
	{
		$this->smmeRepository = $smmeRepository;
	}

	public function exportToExcel(Request $request): BinaryFileResponse
	{
		$query = $this->smmeRepository->query();

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('name', 'like', "%{$search}%")
					->orWhere('registration_number', 'like', "%{$search}%");
			});
		}

		$smmes = $query->get();

		if ($smmes->isEmpty()) {
			return $this->generateEmptyExcel();
		}

		return $this->generateExcel($smmes);
	}

	private function generateExcel($smmes): BinaryFileResponse
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$headers = [
			'A1' => 'Name',
			'B1' => 'Reg Number',
			'C1' => 'Years Exp',
			'D1' => 'Team Composition',
			'E1' => 'Grade',
			'F1' => 'Status',
			'G1' => 'Docs Verified',
			'H1' => 'Company Reg',
			'I1' => 'Tax Cert',
			'J1' => 'BEE Cert',
			'K1' => 'Profile',
		];

		foreach ($headers as $cell => $label) {
			$sheet->setCellValue($cell, $label);
		}

		$sheet->getStyle('A1:K1')->applyFromArray([
			'font' => ['bold' => true],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => ['rgb' => 'DDDDDD'],
			],
		]);

		$row = 2;
		foreach ($smmes as $smme) {
			$sheet->setCellValue("A$row", $smme->name);
			$sheet->setCellValue("B$row", $smme->registration_number);
			$sheet->setCellValue("C$row", $smme->years_of_experience);
			$sheet->setCellValue("D$row", $smme->team_composition);
			$sheet->setCellValue("E$row", $smme->grade);
			$sheet->setCellValue("F$row", $smme->status);
			$sheet->setCellValue("G$row", $smme->documents_verified ? 'Yes' : 'No');
			$sheet->setCellValue("H$row", $smme->company_registration);
			$sheet->setCellValue("I$row", $smme->tax_certificate);
			$sheet->setCellValue("J$row", $smme->bee_certificate);
			$sheet->setCellValue("K$row", $smme->company_profile);
			$row++;
		}

		foreach (range('A', 'K') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		return $this->saveAndDownload($spreadsheet, 'smmes_export');
	}

	private function generateEmptyExcel(): BinaryFileResponse
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'No SMMEs found');
		$sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
		$sheet->mergeCells('A1:E1');

		return $this->saveAndDownload($spreadsheet, 'empty_smmes_export');
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