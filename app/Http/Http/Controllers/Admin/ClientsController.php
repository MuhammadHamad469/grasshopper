<?php

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Models\Client;
use App\Models\InvoiceItem;
use Barryvdh\DomPDF\PDF;
use App\Repositories\ClientRepositoryInterface;
use App\Http\Requests\Admin\ClientRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use App\Services\InvoiceExportService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Carbon;

class ClientsController extends Controller
{
	protected ClientRepositoryInterface $clientRepository;

	public function __construct(ClientRepositoryInterface $clientRepository)
	{
		$this->clientRepository = $clientRepository;
        $this->moduleName = 'Client Management';

        view()->share('moduleName', $this->moduleName);
	}

	public function index(Request $request)
	{
		$clients = Client::all();

        return view('admin.clients.index', compact('clients'));
	}

	public function create()
	{
		if (!Gate::allows('has_permission','can_create_invoices')) {
			return view('errors.403');
		}

		return view('admin.clients.create');
	}

	public function show($id)
    {
        // if (! Gate::allows('role_view')) {
        //     return abort(401);
        // }
        // $users = \App\Models\User::where('role_id', $id)->get();

        // $role = Role::findOrFail($id);

        // return view('admin.roles.show', compact('role', 'users'));
    }

	public function store(ClientRequest $request)
	{
		$validatedData = $request->validated();

		$client = $this->clientRepository->create($validatedData);
		$encryptedPassword = Crypt::encryptString($request->db_password);
		$client->update(['db_password' => $encryptedPassword]);

		return redirect()->route('admin.clients.create', $client)->with('success', 'Client created successfully.');
	}

	public function edit(Client $client)
	{
		if (!Gate::allows('has_permission','can_view_invoices')) {
			return view('errors.403');
		}

		$plainPassword = !empty($client->db_password) ? Crypt::decryptString($client->db_password) : '';

		return view('admin.clients.edit', compact('client','plainPassword'));
	}

	public function update(ClientRequest $request, Client $client)
	{
		$validatedData = $request->validated();

		$this->clientRepository->update($client->id, $validatedData);
		$encryptedPassword = Crypt::encryptString($request->db_password);
		$client->update(['db_password' => $encryptedPassword]);

		return redirect()->route('admin.clients.index')->with('success', 'Client updated successfully.');
	}

	public function destroy(Client $client)
	{
		$this->clientRepository->delete($client->id);
		return redirect()->route('admin.clients.index')->with('success', 'Client deleted successfully.');
	}

	public function dashboard(Request $request, $id)
	{
	    if ($request->filled('start_date') && $request->filled('end_date')) {
	        $startDate = Carbon::parse($request->input('start_date'))->startOfDay();
	        $endDate   = Carbon::parse($request->input('end_date'))->endOfDay();
	    } else {
	        $dateRange = getFinancialYearDates();
		    $startDate = Carbon::now()->subDays(30);
		    $endDate   = Carbon::now();
	    }

	    $client = Client::findOrFail($id);
	    $password = $client->db_password != '' ? Crypt::decryptString($client->db_password) : '';

	    config([
	        'database.connections.dynamic_client' => [
	            'driver'    => 'mysql',
	            'host'      => $client->db_host,
	            'port'      => $client->db_port,
	            'database'  => $client->db_name,
	            'username'  => $client->db_username,
	            'password'  => $password,
	            'charset'   => 'utf8mb4',
	            'collation' => 'utf8mb4_unicode_ci',
	            'prefix'    => '',
	        ]
	    ]);

		$totalUsers                      = DB::connection('dynamic_client')->table('users')->get();
		$totalUserAverageSessionDuration = DB::connection('dynamic_client')
		    ->table('user_sessions')
		    ->leftJoin('users' , 'users.id' , 'user_sessions.user_id')
		    ->select(
		        'user_id',
		        'users.name as user_name',
		        DB::raw('SUM(duration_seconds/3600) AS session_time'),
		        DB::raw('COUNT(user_sessions.id) AS user_login_count'),
		        DB::raw('COUNT(user_sessions.id) AS user_count')
		    )
    	    ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
		        $q->whereBetween(DB::raw('DATE(user_sessions.created_at)'), [$startDate, $endDate]);
		    })
		    ->whereNotNull('users.name')
		    ->groupBy('user_id')
		    ->get();

		$user_growth = [];
		$total_users_count = [];
		$months            = [];
		$yr_month_date     = [];
		$users_name        = [];

		for ($m=1; $m<=12; $m++) {
			 $month = date('F', mktime(0,0,0,$m, 1, date('Y')));

			 if($m <= 9){
				 $date = date('Y') . '-' . '0' . $m;
			 }elseif($m > 9){
				 $date = date('Y') . '-' . $m;
			}

			$users = DB::connection('dynamic_client')->table('users')
				->where('created_at','LIKE','%' . $date . '%')
				->get();

			$total_users_count[] = count($users);
			$months[]            = $month;
			$yr_month_date[]     = $date;
			$users_name[]        = $users->pluck('name')->toArray();
		}

		$usersQuery = DB::connection('dynamic_client')->table('users');
	    $totalClients = $usersQuery->when(
	        $request->filled('start_date') && $request->filled('end_date'),
	        fn($query) => $query->whereBetween(DB::raw('DATE(created_at)'), [$startDate->toDateString(), $endDate->toDateString()])
	    )->count();

		$moduleUsageLogs = DB::connection('dynamic_client')
		    ->table('module_usage_logs')
		    ->select('module_name', DB::raw('SUM(duration_seconds/3600) as usage_time'))
		    ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
		        $q->whereBetween(DB::raw('DATE(start_time)'), [$startDate->toDateString(), $endDate->toDateString()]);
		    })
		    ->where('module_name','<>','unknown')
		    ->groupBy('module_name')
		    ->orderByDesc('usage_time')
		    ->get();

		$modulesUsageLabels = $moduleUsageLogs->pluck('module_name')->toArray();
		$modulesUsageCounts = $moduleUsageLogs->pluck('usage_time')->toArray();

	    return view('admin.clients.dashboard', [
			'client'                          => $client,
			'totalClients'                    => $totalClients,
			'total_users_count'               => $total_users_count,
			'months'              			  => $months,
			'startDate'                       => $startDate,
			'endDate'                         => $endDate,
			'modulesUsageLabels'              => $modulesUsageLabels,
			'modulesUsageCounts'              => $modulesUsageCounts,
			'totalUsers'                      => $totalUsers,
			'totalUserAverageSessionDuration' => $totalUserAverageSessionDuration,
	    ]);
	}

	public function getActiveInactiveData(Request $request)
	{
	    $filter = $request->get('filter', 'three_month');

		if ($filter === 'monthly') {
		    $startDate = Carbon::now()->subDays(30);
		    $endDate   = Carbon::now()->endOfMonth();
		} elseif ($filter === 'yearly') {
		    $startDate = Carbon::now()->startOfYear();
		    $endDate   = Carbon::now()->endOfYear();
		} else {
		    $startDate = Carbon::now()->subDays(90);
		    $endDate   = Carbon::now()->endOfWeek();
		}

	    $activeClients = Client::whereBetween('last_activity', [$startDate, $endDate])
		    ->distinct('id')
		    ->count('id');

	    $totalClients = Client::count();

		$inactiveClients = max(0, $totalClients - $activeClients);

	    $result = [
	        'labels' => ['Active Clients', 'Inactive Clients'],
	        'values' => [$activeClients, $inactiveClients],
	    ];

	    return response()->json([
			'success' => true,
			'data'    => $result
	    ]);
	}

	public function getModulesUsageData(Request $request)
	{
	    $client = Client::findOrFail($request->client_id);
	    $filter = $request->get('filter', 'monthly');

		$password = $client->db_password != '' ? Crypt::decryptString($client->db_password) : '';
	    // Set dynamic connection
	    config([
	        'database.connections.dynamic_client' => [
	            'driver'    => 'mysql',
	            'host'      => $client->db_host,
	            'port'      => $client->db_port,
	            'database'  => $client->db_name,
	            'username'  => $client->db_username,
	            'password'  => $password,
	            'charset'   => 'utf8mb4',
	            'collation' => 'utf8mb4_unicode_ci',
	            'prefix'    => '',
	        ]
	    ]);

	    // Determine date range based on filter
		if ($filter === 'weekly') {
		    $startDate = Carbon::now()->subDays(7);
		    $endDate   = Carbon::now()->endOfWeek();
		} elseif ($filter === 'monthly') {
		    $startDate = Carbon::now()->subDays(30);
		    $endDate   = Carbon::now()->endOfMonth();
		} elseif ($filter === 'yearly') {
		    $startDate = Carbon::now()->startOfYear();
		    $endDate   = Carbon::now()->endOfYear();
		}


		$moduleUsageLogs = DB::connection('dynamic_client')
	    ->table('module_usage_logs')
	    ->select('module_name', DB::raw('SUM(duration_seconds/3600) as usage_time'))
	    ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
	        $q->whereBetween(DB::raw('DATE(start_time)'), [$startDate, $endDate]);
	    })
	    ->groupBy('module_name')
	    ->orderByDesc('usage_time')
	    ->get();


		$modulesUsageLabels = $moduleUsageLogs->pluck('module_name')->toArray();
		$modulesUsageCounts = $moduleUsageLogs->pluck('usage_time')->toArray();

	    return response()->json([
			'success' => true,
			'data' => [
				'modulesUsageLabels' => $modulesUsageLabels,
				'modulesUsageCounts' => $modulesUsageCounts,
			]
	    ]);
	}

	public function getUserSessionsData(Request $request)
	{
	    $client = Client::findOrFail($request->client_id);
	    $filter = $request->get('filter', 'monthly');

	    // Set dynamic connection
		$password = $client->db_password != '' ? Crypt::decryptString($client->db_password) : '';

	    config([
	        'database.connections.dynamic_client' => [
	            'driver'    => 'mysql',
	            'host'      => $client->db_host,
	            'port'      => $client->db_port,
	            'database'  => $client->db_name,
	            'username'  => $client->db_username,
	            'password'  => $password,
	            'charset'   => 'utf8mb4',
	            'collation' => 'utf8mb4_unicode_ci',
	            'prefix'    => '',
	        ]
	    ]);

	    // Determine date range based on filter
		if ($filter === 'weekly') {
		    $startDate = Carbon::now()->subDays(7);
		    $endDate   = Carbon::now();
		    $divide_by = 7;
		} elseif ($filter === 'monthly') {
		    $startDate = Carbon::now()->subDays(30);
		    $endDate   = Carbon::now();
		    $divide_by = 30;
		} elseif ($filter === 'yearly') {
		    $startDate = Carbon::now()->startOfYear();
		    $endDate   = Carbon::now()->endOfYear();
		    $divide_by = 365;
		}

		$totalUserAverageSessionDuration = DB::connection('dynamic_client')
	    ->table('user_sessions')
	    ->leftJoin('users', 'users.id', '=', 'user_sessions.user_id')
	    ->select(
	        'user_id',
	        'users.name as user_name',
	        DB::raw('SUM(duration_seconds/3600) AS session_time'),
	        DB::raw('COUNT(user_id) AS user_login_count'),
	        DB::raw('user_sessions.created_at')
	    )
	    ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
	        $q->whereBetween(DB::raw('DATE(user_sessions.created_at)'), [$startDate, $endDate]);
	    })
	    ->groupBy('users.name')
	    ->get();

	    return response()->json([
			'success' => true,
			'division'=> $divide_by,
			'data'    => [
				'totalUserAverageSessionDuration' => $totalUserAverageSessionDuration,
			]
	    ]);
	}

}