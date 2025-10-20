<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\PDF;
use App\Repositories\InvoiceRepositoryInterface;
use App\Http\Requests\Admin\InvoiceRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Services\InvoiceExportService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InvoiceController extends Controller
{
	protected InvoiceRepositoryInterface $invoiceRepository;

	public function __construct(InvoiceRepositoryInterface $invoiceRepository)
	{
		$this->invoiceRepository = $invoiceRepository;
		$this->moduleName = 'Finance Management';

		view()->share('moduleName', $this->moduleName);
	}

	public function index(Request $request)
	{
		$query = Invoice::query();

		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('client_name', 'like', "%{$search}%");
			});
		}
		if ($request->filled('total_amount_range')) {
			$range = $request->total_amount_range;

			if ($range === '150000+') {
				// For amounts greater than 150000
				$query->where('total_amount', '>', 150000);
			} else {
				// For other ranges
				list($min, $max) = explode('-', $range);
				$query->whereBetween('total_amount', [$min, $max]);
			}
		}
		if ($request->filled('issue_date')) {
			$preset = $request->issue_date;
			if ($preset == 'last_7_days') {
				$query->whereBetween('issue_date', [now()->subDays(7), now()]);
			} elseif ($preset == 'last_30_days') {
				$query->whereBetween('issue_date', [now()->subDays(30), now()]);
			} elseif ($preset == 'last_6_months') {
				$query->whereBetween('issue_date', [now()->subMonths(6), now()]);
			} elseif ($preset == 'last_year') {
				$query->whereBetween('issue_date', [now()->subYear(), now()]);
			}
		}
		if ($request->filled('expiry_date')) {
			$preset = $request->expiry_date;

			if ($preset == 'next_7_days') {
				$query->whereBetween('expiry_date', [now(), now()->addDays(7)]);
			} elseif ($preset == 'next_30_days') {
				$query->whereBetween('expiry_date', [now(), now()->addDays(30)]);
			} elseif ($preset == 'next_6_months') {
				$query->whereBetween('expiry_date', [now(), now()->addMonths(6)]);
			} elseif ($preset == 'next_year') {
				$query->whereBetween('expiry_date', [now(), now()->addYear()]);
			}
		}
		$sortColumn = $request->get('sort', 'client_name');
		$sortDirection = $request->get('direction', 'asc');
		$allowedColumns = ['client_name', 'total_amount', 'issue_date', 'expiry_date'];

		if (in_array($sortColumn, $allowedColumns)) {
			$query->orderBy($sortColumn, $sortDirection);
		}

		$invoices = $query->latest()->paginate(10);

		return view('tenant.invoices.index', compact('invoices'));
	}


	public function create()
	{
		if (!Gate::allows('has_permission', 'can_create_invoices')) {
			return view('errors.403');
		}
		$nextInvoiceNumber = $this->generateNextInvoiceNumber();
		return view('tenant.invoices.create', compact('nextInvoiceNumber'));
	}

	public function store(InvoiceRequest $request)
	{
		$validatedData = $request->validated();

		$invoice = $this->invoiceRepository->create($validatedData);

		$totalAmount = 0;
		foreach ($validatedData['items'] as $item) {
			$amount = $item['quantity'] * $item['unit_price'] * (1 + $item['vat_rate'] / 100);
			$totalAmount += $amount;

			InvoiceItem::create([
				'invoice_id' => $invoice->id,
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit_price' => $item['unit_price'],
				'vat_rate' => $item['vat_rate'],
				'amount' => $amount,
			]);
		}

		$this->invoiceRepository->update($invoice->id, ['total_amount' => $totalAmount]);

		return redirect()->route('tenant.invoices.create', $invoice)->with('success', 'Invoice created successfully.');
	}

	public function show(Invoice $invoice)
	{
		if (!Gate::allows('has_permission', 'can_view_invoices')) {
			return view('errors.403');
		}

		$invoice->load('items');
		return view('tenant.invoices.show', compact('invoice'));
	}

	public function edit(Invoice $invoice)
	{
		if (!Gate::allows('has_permission', 'can_view_invoices')) {
			return view('errors.403');
		}

		$invoice->load('items');
		return view('tenant.invoices.edit', compact('invoice'));
	}

	public function update(InvoiceRequest $request, Invoice $invoice)
	{
		$validatedData = $request->validated();

		$this->invoiceRepository->update($invoice->id, $validatedData);

		$invoice->items()->delete();

		$totalAmount = 0;
		foreach ($validatedData['items'] as $item) {
			InvoiceItem::create([
				'invoice_id' => $invoice->id,
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit_price' => $item['unit_price'],
				'vat_rate' => $item['vat_rate'],
				'amount' => $item['amount'],
			]);
			$totalAmount += $item['amount'];
		}

		$this->invoiceRepository->update($invoice->id, ['total_amount' => $totalAmount]);

		return redirect()->route('tenant.invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
	}


	
	public function exportToExcel(Request $request, InvoiceExportService $exportService): BinaryFileResponse
	{
		return $exportService->exportToExcel($request);
	}



	public function destroy(Invoice $invoice)
	{
		$this->invoiceRepository->delete($invoice->id);
		return redirect()->route('tenant.invoices.index')->with('success', 'Invoice deleted successfully.');
	}

	private function generateNextInvoiceNumber(): string
	{
		$lastInvoice = $this->invoiceRepository->getLastInvoice();
		if (!$lastInvoice) {
			return 'INV0001';
		}

		$lastNumber = intval(substr($lastInvoice->invoice_number, 3));
		$nextNumber = $lastNumber + 1;
		return 'INV' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
	}

	public function generatePDF(Invoice $invoice, PDF $pdf)
	{
		$html = view('tenant.invoices.pdf', compact('invoice'))->render();

		$pdf->loadHTML($html);

		$pdf->setPaper('A4', 'portrait');

		return $pdf->stream('invoice_' . $invoice->invoice_number . '.pdf');
	}
}
