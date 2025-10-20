<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\{Quote, Invoice};
use App\Repositories\InvoiceRepository;
use App\Repositories\QuoteRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FinanceDashboardController extends Controller
{
	protected InvoiceRepository $invoiceRepository;
	protected QuoteRepository $quoteRepository;

	public function __construct(
		InvoiceRepository $invoiceRepository,
		QuoteRepository $quoteRepository
	) {
		$this->invoiceRepository = $invoiceRepository;
		$this->quoteRepository   = $quoteRepository;
		$this->moduleName        = 'Finance Management';

		view()->share('moduleName', $this->moduleName);
	}
	public function getTotalRevenueByMonth()
	{
		return Invoice::whereNotNull('invoice_number') // or whatever identifies real invoices
			->selectRaw('MONTH(issue_date) as month, SUM(total_amount) as total_revenue')
			->groupBy('month')
			->orderBy('month')
			->get()
			->pluck('total_revenue', 'month')
			->map(fn($value) => (float) number_format($value, 2, '.', ''));
	}



	public function index()
	{
		$totalInvoiceAmount = $this->invoiceRepository->getOverallTotalString();
		// Total Quoted Amount
		$totalQuotedAmount = $this->quoteRepository->getTotalAmount();

		// Overdue Invoices
		$overdueInvoices = $this->invoiceRepository->getOverdueInvoices();
		$overdueInvoicesCount = $overdueInvoices->count();

		// Quote to Invoice Conversion Rate
		$conversionRate = $this->calculateQuoteToInvoiceConversionRate();

		// ✅ Updated monthly revenue (includes all invoices, not just current year)

		$monthlyRevenueData = $this->getTotalRevenueByMonth();



		// Fill missing months with 0
		$fullMonthData = collect(range(1, 12))->mapWithKeys(function ($month) use ($monthlyRevenueData) {
			return [$month => $monthlyRevenueData->get($month, 0)];
		});

		$monthlyRevenueLabels = collect(range(1, 12))
			->map(function ($month) {
				return Carbon::create(null, $month)->format('M');
			});

		// Quote Status Data
		$quoteStatusData = $this->calculateQuoteStatusData();

		// Top Clients
		$topClients = $this->getTopClientsByInvoiceAmount();
		$topClientsLabels = $topClients->keys()->toArray();
		$topClientsData = $topClients->values()->toArray();

		// Revenue Comparison
		$revenueComparisonData = [
			$this->invoiceRepository->getOverallTotal(),
			$this->quoteRepository->getTotalAmount()
		];

		// Invoice Ageing Analysis
		$invoiceAgeingData = $this->calculateInvoiceAgeingData();

		// Payment Method Distribution
		$paymentMethodData = $this->calculatePaymentMethodData();

		// Recent Invoices
		$recentInvoices = $this->invoiceRepository
			->query()
			->orderBy('created_at', 'desc')
			->limit(5)
			->get();

		$monthlyRevenueData = $fullMonthData;

		return view('tenant.dashboards.finance', array_merge(compact(
			'totalInvoiceAmount',
			'totalQuotedAmount',
			'overdueInvoicesCount',
			'conversionRate',
			'monthlyRevenueData',
			'monthlyRevenueLabels',
			'quoteStatusData',
			'topClientsLabels',
			'topClientsData',
			'revenueComparisonData',
			'invoiceAgeingData',
			'paymentMethodData',
			'recentInvoices'
		)));
	}


	protected function calculateQuoteToInvoiceConversionRate()
	{
		// Count of converted quotes vs total quotes
		$totalQuotes = $this->quoteRepository->getTotalQuotesCount();
		$convertedQuotes = $this->invoiceRepository->getConvertedQuotesCount();

		if ($totalQuotes == 0) {
			return 0;
		}

		return round(($convertedQuotes / $totalQuotes) * 100, 2);
	}



	protected function calculateQuoteStatusData(): array
	{
		$convertedQuotes = $this->quoteRepository
			->query()
			->where('converted_to_invoice', 1)
			->count();

		$expiredQuotes = $this->quoteRepository
			->query()
			->where(function ($q) {
				$q->whereNull('converted_to_invoice')->orWhere('converted_to_invoice', 0);
			})
			->whereDate('expiry_date', '<', now())
			->count();

		$pendingQuotes = $this->quoteRepository
			->query()
			->where(function ($q) {
				$q->whereNull('converted_to_invoice')->orWhere('converted_to_invoice', 0);
			})
			->whereDate('expiry_date', '>=', now())
			->count();

		return [
			$convertedQuotes,
			$expiredQuotes,
			$pendingQuotes
		];
	}



	protected function getTopClientsByInvoiceAmount($limit = 5)
	{
		return $this->invoiceRepository
			->query()
			->select('client_name', DB::raw('SUM(total_amount) as total_invoiced'))
			->groupBy('client_name')
			->orderByDesc('total_invoiced')
			->limit($limit)
			->pluck('total_invoiced', 'client_name');
	}

	protected function calculateInvoiceAgeingData()
	{
		$now = Carbon::now();

		// Base query for unpaid invoices (only consider those not fully paid)
		$invoices = $this->invoiceRepository
			->query()
			->get(['issue_date', 'expiry_date']);

		// Counters
		$range_0_30 = 0;
		$range_31_60 = 0;
		$range_61_90 = 0;
		$range_90_plus = 0;

		foreach ($invoices as $invoice) {
			// Prefer expiry_date if available, else issue_date
			$referenceDate = $invoice->expiry_date ?? $invoice->issue_date;
			$age = $now->diffInDays(Carbon::parse($referenceDate));

			if ($age <= 30) {
				$range_0_30++;
			} elseif ($age <= 60) {
				$range_31_60++;
			} elseif ($age <= 90) {
				$range_61_90++;
			} else {
				$range_90_plus++;
			}
		}

		return [
			$range_0_30,
			$range_31_60,
			$range_61_90,
			$range_90_plus
		];
	}
	protected function calculatePaymentMethodData()
	{
		// This is a placeholder and should be replaced with actual payment method tracking
		return [
			0, // Bank Transfer
			0, // Credit Card
			0, // PayPal
			0, // Stripe
			0  // Other
		];
	}
}
