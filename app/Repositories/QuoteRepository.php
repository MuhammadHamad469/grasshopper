<?php

namespace App\Repositories;

use App\Models\Invoice;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class QuoteRepository implements QuoteRepositoryInterface
{
	public function getAllQuotes($filters = [], $perPage = 15): LengthAwarePaginator
	{
		$query = $this->query();
		$this->applyFilters($query, $filters);
		return $query->paginate($perPage);
	}

	public function findById(int $id): ?Quote
	{
		return $this->query()->find($id);
	}

	public function create(array $data): Quote
	{
		return Quote::create($data);
	}

	public function update(int $id, array $data): bool
	{
		$quote = $this->findById($id);
		if (!$quote) {
			return false;
		}
		return $quote->update($data);
	}

	public function delete(int $id): bool
	{
		$quote = $this->findById($id);
		if (!$quote) {
			return false;
		}
		return $quote->delete();
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

	public function getExpiredQuotes(): Collection
	{
		return $this->query()
				->where('expiry_date', '<', Carbon::now())
				->get();
	}

	public function getTotalAmount($filters = []): float
	{
		$query = $this->query();
		$this->applyFilters($query, $filters);
		return $query->sum('total_amount');
	}

	public function getTotalQuotesCount()
	{
		return Quote::count();
	}

	public function getLastQuote(): ?Quote
	{
		return $this->query()->latest()->first();
	}

	public function getLastThreeMonthsQuotedTotal()
	{
		$startDate = Carbon::now()->subMonths(2)->startOfMonth();
		$endDate = Carbon::now()->endOfMonth();

		return Quote::selectRaw('MONTH(issue_date) as month, SUM(total_amount) as quoted_amount')
				->whereBetween('issue_date', [$startDate, $endDate])
				->groupBy('month')
				->orderBy('month')
				->get()
				->pluck('quoted_amount', 'month')
				->map(function ($value) {
					return round($value, 2);
				});
	}

	public function query()
	{
		return Quote::query();
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