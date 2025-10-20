<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Repositories\LocationRepository;
use Illuminate\Http\Request;

class LocationController extends Controller
{
	protected $locationRepository;

	public function __construct(LocationRepository $locationRepository)
	{
		$this->locationRepository = $locationRepository;
        $this->moduleName = 'Data Config';

        view()->share('moduleName', $this->moduleName);
	}

	public function index()
	{
		$locations = $this->locationRepository->paginate(10);

		return view('tenant.locations.index', compact('locations'));
	}

	public function create()
	{
		return view('tenant.locations.create');
	}

	public function store(Request $request)
	{
		$this->validate($request, [
				'name' => 'required|unique:locations',
		]);

		$location = $this->locationRepository->create($request->only('name'));

		return redirect()->route('tenant.locations.index')->with('success', 'Location created successfully');
	}

	public function show(Location $location)
	{
		return view('tenant.locations.show', compact('location'));
	}

	public function edit(Location $location)
	{
		return view('tenant.locations.edit', compact('location'));
	}

	public function update(Request $request, Location $location)
	{
		$this->validate($request, [
				'name' => 'required|unique:locations,name,' . $location->id,
		]);

		$this->locationRepository->update($location->id, $request->only('name'));

		return redirect()->route('tenant.locations.index')->with('success', 'Location updated successfully');
	}

	public function destroy(Location $location)
	{
		$this->locationRepository->delete($location->id);

		return redirect()->route('tenant.locations.index')->with('success', 'Location deleted successfully');
	}
}