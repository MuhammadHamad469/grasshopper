<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Repositories\InvoiceRepositoryInterface;
use App\Services\QuoteExportService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class QuoteController extends Controller
{
	public function __construct()
	{
		$this->moduleName = 'Finance Management';

		view()->share('moduleName', $this->moduleName);
	}

	public function index(Request $request)
	{
		$query = Quote::query();
		// Apply search filter
		if ($request->filled('search')) {
			$search = $request->search;
			$query->where(function ($q) use ($search) {
				$q->where('client_name', 'like', "%{$search}%");
				//->where('quote_number', 'like', "%{$search}%");
			});
		}
		// Apply the total amount filter
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
		// Filter by Preset Date Ranges
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
		// Filter by Preset Expiry Date Ranges
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
		// Apply sorting
		$sortColumn = $request->get('sort', 'client_name');
		$sortDirection = $request->get('direction', 'asc');
		$allowedColumns = ['client_name', 'issue_date', 'expiry_date', 'total_amount'];

		if (in_array($sortColumn, $allowedColumns)) {
			$query->orderBy($sortColumn, $sortDirection);
		}


		$quotes = $query->latest()->paginate(10);
		return view('tenant.quotes.index', compact('quotes'));
	}

	public function create()
	{
		if (!Gate::allows('has_permission', 'can_create_quotes')) {
			return view('errors.403');
		}

		$nextQuoteNumber = $this->generateNextQuoteNumber();
		return view('tenant.quotes.create', compact('nextQuoteNumber'));
	}


	public function store(Request $request)
	{
		$validatedData = $request->validate([
			'client_name' => 'required|string',
			'client_address' => 'required|string',
			'issue_date' => 'required|date',
			'expiry_date' => 'required|date',
			'quote_number' => 'required|string|unique:quotes',
			'vat_number' => 'required|string',
			'company_name' => 'required|string',
			'company_address' => 'required|string',
			'total_amount' => 'nullable|numeric|min:0|max:10000000', // max 10M total
			'items' => 'required|array',
			'items.*.description' => 'required|string',
			'items.*.quantity' => 'required|integer|min:1|max:100000', // limit quantity
			'items.*.unit_price' => 'required|numeric|min:0|max:100000', // limit unit price
			'items.*.vat_rate' => 'required|numeric|min:0|max:100', // VAT rate 0–100
		]);

		$quote = Quote::create($validatedData);

		$totalAmount = 0;
		foreach ($validatedData['items'] as $item) {
			$amount = $item['quantity'] * $item['unit_price'] * (1 + $item['vat_rate'] / 100);

			// extra safeguard in case overflow
			if ($amount > 10000000) {
				return back()->withErrors(['items' => 'Item amount exceeds the maximum allowed limit.'])->withInput();
			}

			$totalAmount += $amount;

			QuoteItem::create([
				'quote_id'    => $quote->id,
				'description' => $item['description'],
				'quantity'    => $item['quantity'],
				'unit_price'  => $item['unit_price'],
				'vat_rate'    => $item['vat_rate'],
				'amount'      => $amount,
			]);
		}

		if ($totalAmount > 10000000) { // global max check
			return back()->withErrors(['total_amount' => 'Total amount exceeds the maximum allowed limit.'])->withInput();
		}

		$quote->update(['total_amount' => $totalAmount]);

		return redirect()->route('tenant.quotes.create', $quote)->with('success', 'Quote created successfully.');
	}


	public function generateInvoice(Quote $quote, InvoiceRepositoryInterface $invoiceRepo)
	{

		if (!Gate::allows('has_permission', 'can_create_invoices')) {
			return view('errors.403');
		}

		$invoiceData = [
			'client_name' => $quote->client_name,
			'client_address' => $quote->client_address,
			'company_name' => $quote->company_name,
			'company_address' => $quote->company_address,
			'vat_number' => $quote->vat_number,
			'issue_date' => now()->format('Y-m-d'),
			'expiry_date' => now()->addDays(30)->format('Y-m-d'),
			'invoice_number' => $this->generateNextInvoiceNumber(),
			'total_amount' => $quote->total_amount,
			'quote_id' => $quote->id,
		];

		DB::beginTransaction();
		try {
			// Create invoice using repository
			$invoice = $invoiceRepo->create($invoiceData);

			// Copy all quote items to invoice items
			foreach ($quote->items as $item) {
				InvoiceItem::create([
					'invoice_id' => $invoice->id,
					'description' => $item->description,
					'quantity' => $item->quantity,
					'unit_price' => $item->unit_price,
					'vat_rate' => $item->vat_rate,
					'amount' => $item->quantity * $item->unit_price * (1 + $item->vat_rate / 100),
				]);
			}

			// Mark quote as converted
			$quote->update([
				'converted_to_invoice' => true,
				'invoice_id' => $invoice->id
			]);
			DB::commit();

			return redirect()->route('tenant.invoices.show', $invoice)
				->with('success', 'Invoice generated from quote successfully!');
		} catch (\Exception $e) {
			DB::rollBack();
			return back()->with('error', 'Failed to generate invoice: ' . $e->getMessage());
		}
	}

	protected function generateNextInvoiceNumber(): string
	{
		// Option 1: Simple incremental number
		$lastInvoice = \App\Models\Invoice::latest()->first();
		$number = $lastInvoice ? (int) str_replace('INV', '', $lastInvoice->invoice_number) + 1 : 1;

		return 'INV' . str_pad($number, 5, '0', STR_PAD_LEFT);
	}


	public function show(Quote $quote)
	{
		if (!Gate::allows('has_permission', 'can_view_quotes')) {
			return view('errors.403');
		}
		$quote->load('items'); // Ensure quote items are loaded
		return view('tenant.quotes.show', compact('quote'));
	}

	public function edit(Quote $quote)
	{
		if (!Gate::allows('has_permission', 'can_edit_quotes')) {
			return view('errors.403');
		}
		$quote->load('items'); // Ensure quote items are loaded
		return view('tenant.quotes.edit', compact('quote'));
	}

	public function update(Request $request, Quote $quote)
	{
		$validatedData = $request->validate([
			'client_name' => 'required|string',
			'client_address' => 'required|string',
			'issue_date' => 'required|date',
			'expiry_date' => 'required|date',
			'quote_number' => 'required|string|unique:quotes,quote_number,' . $quote->id,
			'vat_number' => 'required|string',
			'company_name' => 'required|string',
			'company_address' => 'required|string',
			'items' => 'required|array',
			'items.*.description' => 'required|string',
			'items.*.quantity' => 'required|integer|min:1',
			'items.*.unit_price' => 'required|numeric|min:0',
			'items.*.vat_rate' => 'required|numeric|min:0',
			'items.*.amount' => 'required|numeric|min:0',
		]);

		$quote->update($validatedData);

		// Delete existing items
		$quote->items()->delete();

		// Create new items
		$totalAmount = 0;
		foreach ($validatedData['items'] as $item) {
			QuoteItem::create([
				'quote_id' => $quote->id,
				'description' => $item['description'],
				'quantity' => $item['quantity'],
				'unit_price' => $item['unit_price'],
				'vat_rate' => $item['vat_rate'],
				'amount' => $item['amount'],
			]);
			$totalAmount += $item['amount'];
		}

		// Update total amount
		$quote->update(['total_amount' => $totalAmount]);

		return redirect()->route('tenant.quotes.show', $quote)->with('success', 'Quote updated successfully.');
	}


	public function destroy(Quote $quote)
	{
		if (!Gate::allows('has_permission', 'can_delete_quotes')) {
			return view('errors.403');
		}
		$quote->delete();
		return redirect()->route('tenant.quotes.index')->with('success', 'Quote deleted successfully.');
	}

	private function generateNextQuoteNumber()
	{
		$lastQuote = Quote::latest()->first();
		if (!$lastQuote) {
			return 'Q0001';
		}

		$lastNumber = intval(substr($lastQuote->quote_number, 1));
		$nextNumber = $lastNumber + 1;
		return 'Q' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
	}

	public function generatePDF(Quote $quote, PDF $pdf)
	{
		$html = view('tenant.quotes.pdf', compact('quote'))->render();

		$pdf->loadHTML($html);

		$pdf->setPaper('A4', 'portrait');

		return $pdf->stream('quote_' . $quote->quote_number . '.pdf');
	}

	public function exportToExcel(Request $request, QuoteExportService $exportService): BinaryFileResponse
	{
		return $exportService->exportToExcel($request);
	}
}
