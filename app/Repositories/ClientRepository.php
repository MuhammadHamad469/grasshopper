<?php

namespace App\Repositories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class ClientRepository implements ClientRepositoryInterface
{
	public function getOverallTotal($filters = []): float
	{
		$query = $this->query();
		$this->applyFilters($query, $filters);
		return $query->sum('total_amount');
	}

	public function getOverallTotalString($filters = []): string
	{

		$totalAmount = $this->getOverallTotal($filters);

		$billion = 1000000000;
		$million = 1000000;
		$thousand = 1000;

		if ($totalAmount >= $billion) {
			return number_format($totalAmount / $billion, 2) . 'B';
		}elseif ($totalAmount >= $million) {
			return number_format($totalAmount / $million, 2) . 'M';
		} elseif ($totalAmount >= $thousand) {
			return number_format($totalAmount / $thousand, 1) . 'k';
		} else {
			return number_format($totalAmount, 2);
		}
	}

	public function getAllInvoices($filters = [], $perPage = 15): LengthAwarePaginator
	{
		$query = $this->query();
		$this->applyFilters($query, $filters);
		return $query->paginate($perPage);
	}

	public function findById(int $id): ?Client
	{
		return $this->query()->find($id);
	}

	public function create(array $data): Client
	{
		return Client::create($data);
	}

	public function update(int $id, array $data): bool
	{
		$invoice = $this->findById($id);
		if (!$invoice) {
			return false;
		}
		return $invoice->update($data);
	}

	public function delete(int $id): bool
	{
		$invoice = $this->findById($id);
		if (!$invoice) {
			return false;
		}
		return $invoice->delete();
	}

	public function getByClientName(string $clientName): Collection
	{
		return $this->query()->where('client_name', 'like', "%{$clientName}%")->get();
	}

	public function getByDateRange($startDate, $endDate): Collection
	{
		return $this->query()
				->whereBetween('issue_date', [$startDate, $endDate])
				->get();
	}

	public function getOverdueInvoices(): Collection
	{
		return $this->query()
				->where('expiry_date', '<', now())
				->where('total_amount', '>', 0)
				->get();
	}

	public function getLastInvoice(): ?Client
	{
		return $this->query()->latest()->first();
	}

	public function getTotalRevenueByMonth($year = 2025)
	{
		$year = $year ?? date('Y');
		return Client::selectRaw('MONTH(issue_date) as month, SUM(total_amount) as total_revenue')
				->whereYear('issue_date', $year)
				->groupBy('month')
				->orderBy('month')
				->get()
				->pluck('total_revenue', 'month')
				->map(function ($value) {
					return round($value, 2);
				});
	}

	public function getLastThreeMonthsInvoicedTotal()
	{
		$startDate = Carbon::now()->subMonths(2)->startOfMonth();
		$endDate = Carbon::now()->endOfMonth();

		return Client::selectRaw('MONTH(issue_date) as month, SUM(total_amount) as total_revenue')
				->whereBetween('issue_date', [$startDate, $endDate])
				->groupBy('month')
				->orderBy('month')
				->get()
				->pluck('total_revenue', 'month')
				->map(function ($value) {
					return round($value, 2);
				});
	}

	public function getConvertedQuotesCount()
	{
//		return Client::whereNotNull('quote_id')->count(); TODO
		return 0;
	}

	public function query(): \Illuminate\Database\Eloquent\Builder
	{
		return Client::query();
	}

	private function applyFilters($query, $filters)
	{
		foreach ($filters as $field => $value) {
			if (is_array($value)) {
				$operator = $value[0] ?? '=';
				$filterValue = $value[1] ?? null;
				$query->where($field, $operator, $filterValue);
			} else {
				$query->where($field, $value);
			}
		}
	}
}