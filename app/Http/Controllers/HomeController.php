<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Project;
use App\Repositories\ProjectRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
	protected ProjectRepositoryInterface $projectRepository;

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct(ProjectRepositoryInterface $projectRepository)
	{
		$this->middleware('auth');
		$this->projectRepository = $projectRepository;
        $this->moduleName = 'Home';

        view()->share('moduleName', $this->moduleName);
	}

	/**
	 * Show the application dashboard with employee profile.
	 *
	 * @return \Illuminate\Contracts\Support\Renderable
	 */
	public function index()
	{
		$user     = auth()->user();
		$employee = Employee::where('user_id', $user->id)
				->orWhere('email', $user->email)
				->first();
		$currentEmployeeProjects = $user->leadingProjects;
		$subordinates            = $user->getSubordinates()->get();
		$subordinatesProjects    = $this->projectRepository->getSubordinatesProjects($user);
		$totalProjects           = $this->projectRepository->getTotalUserProjects();
		$totalCompletedProjects  = $this->projectRepository->getTotalCompletedProjects();
		$userNextDeadline        = $this->projectRepository->getUserNextDeadline();
		$team                    = $user->teamName();
		$formattedDeadline       = $userNextDeadline
				? $userNextDeadline->format('d F Y')
				: 'No Active Project';

		return view('home', compact(
			'totalProjects',
			'totalCompletedProjects',
			'team',
			'formattedDeadline',
			'employee',
			'currentEmployeeProjects',
			'subordinates',
			'subordinatesProjects'
		));
	}

	/**
	 * @param $subordinates
	 * @param \Illuminate\Contracts\Auth\Authenticatable|null $user
	 * @return mixed
	 */
	public function getSubordinatesProjects($subordinates, ?\Illuminate\Contracts\Auth\Authenticatable $user)
	{
		$subordinatesProjects = Project::whereIn('team_leader_user_id', $subordinates->pluck('id'))
				->where('team_leader_user_id', '!=', $user->id)
				->get()
				->groupBy('team_leader_user_id');
		return $subordinatesProjects;
	}

	public function moduleUsage(Request $request)
	{
		if (Auth::check()) {
	        DB::table('module_usage_logs')->insert([
				'user_id'          => Auth::id(),
				'module_name'      => $request->module_name,
				'start_time'       => now()->subSeconds($request->duration_seconds),
				'end_time'         => now(),
				'duration_seconds' => $request->duration_seconds
	        ]);

		    return response()->json([
		    	'success' => true ,
		    	'message' => 'Inserted Successfully',
		    ]);
	    } else{
		    return response()->json([
		    	'success' => false ,
		    	'message' => 'Error Occured',
		    ]);
	    }
	}
}