<?php

namespace App\Repositories;

use App\Models\Smmes;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;

class SmmeRepository implements SmmeRepositoryInterface
{
	public function getAllSmmes($filters = [], $perPage = 15): LengthAwarePaginator
	{
		$query = $this->query();
		$this->applyFilters($query, $filters);
		return $query->paginate($perPage);
	}

	public function findById(int $id): ?Smmes
	{
		return $this->query()->find($id);
	}

	public function create(array $data): Smmes
	{
		return Smmes::create($data);
	}

	public function update(int $id, array $data): bool
	{
		$smme = $this->findById($id);
		if (!$smme) {
			return false;
		}
		return $smme->update($data);
	}

	public function delete(int $id): bool
	{
		$smme = $this->findById($id);
		if (!$smme) {
			return false;
		}
		return $smme->delete();
	}

	public function getByName(string $name): Collection
	{
		return $this->query()->where('name', 'like', "%{$name}%")->get();
	}

	public function getByGrade(string $grade): Collection
	{
		return $this->query()->where('grade', $grade)->get();
	}

	public function getByStatus(string $status): Collection
	{
		return $this->query()->where('status', $status)->get();
	}

	public function getVerifiedSmmes(): Collection
	{
		return $this->query()->where('documents_verified', true)->get();
	}

	public function getSmmeCountByStatus(): array
	{
		return [
				'green' => $this->query()->where('status', 'Green')->count(),
				'yellow' => $this->query()->where('status', 'Yellow')->count(),
				'red' => $this->query()->where('status', 'Red')->count(),
		];
	}


	public function getSmmesByExperience(int $years): Collection
	{
		return $this->query()->where('years_of_experience', '>=', $years)->get();
	}

	public function getLastRegisteredSmme(): ?Smmes
	{
		return $this->query()->latest()->first();
	}

	public function getSmmeCountByGrade()
	{
		return Smmes::selectRaw('grade, COUNT(*) as count')
				->groupBy('grade')
				->orderBy('grade')
				->get()
				->pluck('count', 'grade');
	}

	public function getSmmeRegistrationTrendByMonth($year = 2024)
	{
		$year = $year ?? date('Y');

		return Smmes::selectRaw('MONTH(created_at) as month, COUNT(*) as count')
				->whereYear('created_at', $year)
				->groupBy('month')
				->orderBy('month')
				->get()
				->pluck('count', 'month');
	}

	public function query(): \Illuminate\Database\Eloquent\Builder
	{
		return Smmes::query();
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