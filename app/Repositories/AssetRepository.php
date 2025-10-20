<?php

namespace App\Repositories;

use App\Models\Asset;
use App\Repositories\AssetRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class AssetRepository implements AssetRepositoryInterface
{
	public function getAllAssets(): Collection
	{
		return Asset::all();
	}

	public function getAssetById(int $id): ?Asset
	{
		return Asset::find($id);
	}

	public function createAsset(array $data): Asset
	{
		return Asset::create($data);
	}

	public function updateAsset(int $id, array $data): ?Asset
	{
		$asset = $this->getAssetById($id);
		if ($asset) {
			$asset->update($data);
			return $asset;
		}
		return null;
	}

	public function deleteAsset(int $id): bool
	{
		$asset = $this->getAssetById($id);
		if ($asset) {
			return $asset->delete();
		}
		return false;
	}

	public function getAssetsByStatus(string $status): Collection
	{
		return Asset::where('status', $status)->get();
	}

	public function getAssetsByType(int $assetTypeId): Collection
	{
		return Asset::where('asset_type_id', $assetTypeId)->get();
	}

	public function getAssetsByProject(int $projectId): Collection
	{
		return Asset::where('project_id', $projectId)->get();
	}

	public function countAssetsByStatus(string $status): int
	{
		return Asset::where('status', $status)->count();
	}
}