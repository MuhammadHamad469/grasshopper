<?php

namespace App\Repositories;

use App\Models\Location;

class LocationRepository
{
	public function all()
	{
		return Location::all();
	}

	public function create(array $data)
	{
		return Location::create($data);
	}

	public function find($id)
	{
		return Location::findOrFail($id);
	}

	public function update($id, array $data)
	{
		$location = $this->find($id);
		$location->update($data);
		return $location;
	}

	public function delete($id)
	{
		$location = $this->find($id);
		return $location->delete();
	}

	public function paginate(int $limit=10)
	{
		return Location::paginate($limit);
	}
}