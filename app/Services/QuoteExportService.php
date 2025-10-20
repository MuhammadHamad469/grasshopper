<?php

namespace App\Services;

use App\Repositories\QuoteRepository;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class QuoteExportService
{
	protected $quoteRepository;

	public function __construct(QuoteRepository $quoteRepository)
	{
		$this->quoteRepository = $quoteRepository;
	}

	public function exportToExcel(Request $request): BinaryFileResponse
	{
		$quotes = $this->getFilteredQuotes($request);

		if ($quotes->isEmpty()) {
			return $this->generateEmptyExcel();
		}

		return $this->generateExcel($quotes);
	}

	private function getFilteredQuotes(Request $request)
	{
		$query = $this->quoteRepository->query();

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('client_name', 'like', "%{$search}%")
				  ->orWhere('quote_number', 'like', "%{$search}%");
			});
		}

		if ($request->filled('total_amount_range')) {
			$range = $request->total_amount_range;
			if ($range === '150000+') {
				$query->where('total_amount', '>', 150000);
			} else {
				[$min, $max] = explode('-', $range);
				$query->whereBetween('total_amount', [(float) $min, (float) $max]);
			}
		}

		// Add more filters as needed
		return $query->get();
	}

	private function generateExcel($quotes): BinaryFileResponse
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$headers = [
			'A1' => 'Quote Number',
			'B1' => 'Client Name',
			'C1' => 'Company Name',
			'D1' => 'Total Amount',
			'E1' => 'Issue Date',
			'F1' => 'Expiry Date',
			'G1' => 'VAT Number'
		];

		foreach ($headers as $cell => $value) {
			$sheet->setCellValue($cell, $value);
		}

		$sheet->getStyle('A1:G1')->applyFromArray([
			'font' => ['bold' => true],
			'fill' => [
				'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
				'startColor' => ['rgb' => 'DDDDDD'],
			],
		]);

		$row = 2;
		foreach ($quotes as $quote) {
			$sheet->setCellValue('A' . $row, $quote->quote_number);
			$sheet->setCellValue('B' . $row, $quote->client_name);
			$sheet->setCellValue('C' . $row, $quote->company_name);
			$sheet->setCellValue('D' . $row, $quote->total_amount);
			$sheet->setCellValue('E' . $row, optional($quote->issue_date)->format('Y-m-d'));
			$sheet->setCellValue('F' . $row, optional($quote->expiry_date)->format('Y-m-d'));
			$sheet->setCellValue('G' . $row, $quote->vat_number);
			$row++;
		}

		foreach (range('A', 'G') as $column) {
			$sheet->getColumnDimension($column)->setAutoSize(true);
		}

		return $this->saveAndDownload($spreadsheet, 'quotes_export');
	}

	private function generateEmptyExcel(): BinaryFileResponse
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'No quotes available for the selected filters');
		$sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
		$sheet->mergeCells('A1:G1');

		return $this->saveAndDownload($spreadsheet, 'empty_quotes_export');
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