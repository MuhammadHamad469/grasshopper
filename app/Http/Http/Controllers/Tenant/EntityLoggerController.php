<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\EntityLogger;
use Illuminate\Http\Request;

class EntityLoggerController extends Controller
{
    public function __construct()
    {
        $this->moduleName = 'Data Config';

        view()->share('moduleName', $this->moduleName);
    }

	public function index(Request $request)
	{
		$query = EntityLogger::orderBy('created_at', 'desc');

		if ($request->has('search') && $request->search != '') {
			$search = $request->search;
			$query->where(function($q) use ($search) {
				$q->where('entity_type', 'like', '%' . $search . '%')
						->orWhere('entity_name', 'like', '%' . $search . '%')
						->orWhere('description', 'like', '%' . $search . '%')
						->orWhere('performed_by', 'like', '%' . $search . '%');
			});
		}

		if ($request->has('action_type') && $request->action_type != '') {
			$query->where('action_type', $request->action_type);
		}

		if ($request->has('entity_type') && $request->entity_type != '') {
			$query->where('entity_type', $request->entity_type);
		}

		$entityLogs = $query->paginate(5);

		return view('tenant.logger.index', compact('entityLogs'));
	}
}