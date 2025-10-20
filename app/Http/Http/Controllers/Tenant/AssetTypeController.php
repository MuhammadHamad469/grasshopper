<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\AssetType;
use Illuminate\Http\Request;

class AssetTypeController extends Controller
{
    public function __construct()
    {
        $this->moduleName = 'Data Config';

        view()->share('moduleName', $this->moduleName);
    }

	public function index(Request $request)
	{
		$query = AssetType::query();

		// Filter by Name
		if ($request->filled('name')) {
   			$query->where('name', $request->name);
		}

	    // Fetch asset types for the filter dropdown
	    $assetTypes = AssetType::all();

		//$assetTypes = AssetType::paginate(10);
		$assetTypes = $query->latest()->paginate(10);

		return view('tenant.asset_types.index', compact('assetTypes'));
	}

	public function create()
	{
		return view('tenant.asset_types.create');
	}

	public function store(Request $request)
	{
		$validatedData = $request->validate([
				'name' => 'required|unique:asset_types|max:255',
				'description' => 'nullable',
		]);

		AssetType::create($validatedData);

		return redirect()->route('tenant.asset-types.index')->with('success', 'Asset type created successfully.');
	}

	public function show(AssetType $assetType)
	{
		return view('tenant.asset_types.show', compact('assetType'));
	}

	public function edit(AssetType $assetType)
	{
		return view('tenant.asset_types.edit', compact('assetType'));
	}

	public function update(Request $request, AssetType $assetType)
	{
		$validatedData = $request->validate([
				'name' => 'required|unique:asset_types,name,' . $assetType->id . '|max:255',
				'description' => 'nullable',
		]);

		$assetType->update($validatedData);

		return redirect()->route('tenant.asset-types.index')->with('success', 'Asset type updated successfully.');
	}

	public function destroy(AssetType $assetType)
	{
		$assetType->delete();

		return redirect()->route('tenant.asset-types.index')->with('success', 'Asset type deleted successfully.');
	}
}