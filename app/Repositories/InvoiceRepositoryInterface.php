<?php

namespace App\Repositories;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface InvoiceRepositoryInterface
{
	public function getOverallTotal($filters = []): float;

	public function getOverallTotalString($filters = []): string;

	public function getAllInvoices($filters = [], $perPage = 15): LengthAwarePaginator;

	public function findById(int $id): ?Invoice;

	public function create(array $data): Invoice;

	public function update(int $id, array $data): bool;

	public function delete(int $id): bool;

	public function getByClientName(string $clientName): Collection;

	public function getByDateRange($startDate, $endDate): Collection;

	public function getOverdueInvoices(): Collection;

	public function getLastInvoice(): ?Invoice;

	public function getTotalRevenueByMonth($year = null);

	public function getLastThreeMonthsInvoicedTotal();
}