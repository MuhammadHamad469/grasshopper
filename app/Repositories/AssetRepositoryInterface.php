<?php

namespace App\Repositories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Collection;

interface AssetRepositoryInterface
{
	public function getAllAssets(): Collection;
	public function getAssetById(int $id): ?Asset;
	public function createAsset(array $data): Asset;
	public function updateAsset(int $id, array $data): ?Asset;
	public function deleteAsset(int $id): bool;
	public function getAssetsByStatus(string $status): Collection;
	public function getAssetsByType(int $assetTypeId): Collection;
	public function getAssetsByProject(int $projectId): Collection;
	public function countAssetsByStatus(string $status): int;
}