<?php

namespace App\Repositories;

use App\Models\Quote;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface QuoteRepositoryInterface
{
	public function getAllQuotes($filters = [], $perPage = 15): LengthAwarePaginator;

	public function findById(int $id): ?Quote;

	public function create(array $data): Quote;

	public function update(int $id, array $data): bool;

	public function delete(int $id): bool;

	public function getByClientName(string $clientName): Collection;

	public function getByDateRange($startDate, $endDate): Collection;

	public function getExpiredQuotes(): Collection;

	public function getTotalAmount($filters = []): float;

	public function getLastQuote(): ?Quote;

  public function getLastThreeMonthsQuotedTotal();
}