<?php

namespace App\Repositories;

use App\Models\Smmes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SmmeRepositoryInterface
{
	public function getAllSmmes($filters = [], $perPage = 15): LengthAwarePaginator;

	public function findById(int $id): ?Smmes;

	public function create(array $data): Smmes;

	public function update(int $id, array $data): bool;

	public function delete(int $id): bool;

	public function getByName(string $name): Collection;

	public function getByGrade(string $grade): Collection;

	public function getByStatus(string $status): Collection;

	public function getSmmeCountByStatus(): array;

	public function getVerifiedSmmes(): Collection;

	public function getSmmesByExperience(int $years): Collection;

	public function getLastRegisteredSmme(): ?Smmes;

	public function getSmmeCountByGrade();

	public function getSmmeRegistrationTrendByMonth($year = null);

}