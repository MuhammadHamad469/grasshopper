<?php

use Illuminate\Support\Carbon;

if (!function_exists('getFinancialYearDates')) {
	/**
	 * Get current financial year start/end dates (default: March 1 - Feb 28/29).
	 *
	 * @param int $startMonth (Optional) Custom start month (e.g., 4 for April-March).
	 * @return array ['start' => Carbon, 'end' => Carbon]
	 */
	function getFinancialYearDates(int $startMonth = 3): array
	{
		$today = now();
		$currentYear = $today->year;

		if ($today->month < $startMonth) {
			$start = Carbon::create($currentYear - 1, $startMonth, 1);
			$end = Carbon::create($currentYear, $startMonth - 1, 1)->subDay()->endOfDay();
		} else {
			$start = Carbon::create($currentYear, $startMonth, 1);
			$end = Carbon::create($currentYear + 1, $startMonth - 1, 1)->subDay()->endOfDay();
		}

		return [
				'start' => $start,
				'end'   => $end,
		];
	}
}