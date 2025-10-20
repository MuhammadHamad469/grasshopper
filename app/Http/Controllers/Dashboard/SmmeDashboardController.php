<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Repositories\InvoiceRepository;
use App\Repositories\QuoteRepository;
use App\Repositories\SmmeRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class SmmeDashboardController extends Controller
{
	protected $smmeRepository;

	public function __construct(SmmeRepository $smmeRepository)
	{
		$this->smmeRepository = $smmeRepository;
	}

	public function index()
	{
		// Total SMMEs
		$totalSmmes = $this->smmeRepository->query()->count();

		// Verified SMMEs
		$verifiedSmmes = $this->smmeRepository->getVerifiedSmmes()->count();

		// Average Years of Experience
		$averageExperience = round($this->smmeRepository->query()->avg('years_of_experience'), 1);

		// Last Registered SMME
		$lastRegisteredSmme = $this->smmeRepository->getLastRegisteredSmme();

		// SMME Status Distribution
		$smmeStatusData = $this->smmeRepository->getSmmeCountByStatus();

		// SMME Grade Distribution
		$smmeGradeData = $this->smmeRepository->getSmmeCountByGrade()->toArray();

		// SMME Registration Trend
		$smmeRegistrationTrendData = $this->smmeRepository->getSmmeRegistrationTrendByMonth()->toArray();

		// Recent SMMEs
		$recentSmmes = $this->smmeRepository->query()
				->latest()
				->limit(5)
				->get();

		return view('tenant.dashboards.smme', [
				'totalSmmes' => $totalSmmes,
				'verifiedSmmes' => $verifiedSmmes,
				'averageExperience' => $averageExperience,
				'lastRegisteredSmme' => $lastRegisteredSmme,
				'smmeStatusData' => array_values($smmeStatusData),
				'smmeGradeData' => $smmeGradeData,
				'smmeRegistrationTrendData' => $smmeRegistrationTrendData,
				'recentSmmes' => $recentSmmes
		]);
	}
}