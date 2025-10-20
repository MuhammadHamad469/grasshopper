<?php

namespace App\Services;

use App\Models\Invoice;
use App\Repositories\InvoiceRepository;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceExportService
{
	protected $invoiceRepository;

	public function __construct(InvoiceRepository $invoiceRepository)
	{
		$this->invoiceRepository = $invoiceRepository;
	}

	public function exportToExcel(Request $request): BinaryFileResponse
	{
		$invoices = $this->getFilteredInvoices($request);

		if ($invoices->isEmpty()) {
			return $this->generateEmptyExcel();
		}

		return $this->generateExcel($invoices);
	}

	private function getFilteredInvoices(Request $request)
	{
		$query = $this->invoiceRepository->query();

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function($q) use ($search) {
				$q->where('client_name', 'like', "%{$search}%")
					->orWhere('invoice_number', 'like', "%{$search}%");
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

		// Add more filters if needed
		return $query->get();
	}

	private function generateExcel($invoices): BinaryFileResponse
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		$headers = [
			'A1' => 'Invoice Number',
			'B1' => 'Client Name',
			'C1' => 'Company Name',
			'D1' => 'Total Amount',
			'E1' => 'Issue Date',
			'F1' => 'Due Date',
			'G1' => 'VAT Number',
		];

		foreach ($headers as $cell => $value) {
			$sheet->setCellValue($cell, $value);
		}

		$sheet->getStyle('A1:G1')->applyFromArray([
			'font' => ['bold' => true],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DDDDDD']],
		]);

		$row = 2;
		foreach ($invoices as $invoice) {
			$sheet->setCellValue('A' . $row, $invoice->invoice_number);
			$sheet->setCellValue('B' . $row, $invoice->client_name);
			$sheet->setCellValue('C' . $row, $invoice->company_name);
			$sheet->setCellValue('D' . $row, $invoice->total_amount);
			$sheet->setCellValue('E' . $row, optional($invoice->issue_date)->format('Y-m-d'));
			$sheet->setCellValue('F' . $row, optional($invoice->expiry_date)->format('Y-m-d'));
			$sheet->setCellValue('G' . $row, $invoice->vat_number);
			$row++;
		}

		foreach (range('A', 'G') as $column) {
			$sheet->getColumnDimension($column)->setAutoSize(true);
		}

		return $this->saveAndDownload($spreadsheet, 'invoices_export');
	}

	private function generateEmptyExcel(): BinaryFileResponse
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'No invoices available for the selected filters');
		$sheet->getStyle('A1')->applyFromArray(['font' => ['bold' => true, 'size' => 14]]);
		$sheet->mergeCells('A1:G1');

		return $this->saveAndDownload($spreadsheet, 'empty_invoices_export');
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