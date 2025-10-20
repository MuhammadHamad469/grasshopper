<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class DateService
{
	/**
	 * Calculate working days between two dates
	 * excluding weekends and South African public holidays
	 *
	 * @param Carbon $startDate
	 * @param Carbon $endDate
	 * @return int
	 */
	public function calculateWorkingDays(Carbon $startDate, Carbon $endDate): int
	{
		$workingDays = 0;
		$currentDate = $startDate->copy();

		// Get South African public holidays
		$publicHolidays = $this->getSouthAfricanPublicHolidays($startDate->year, $endDate->year);

		while ($currentDate->lte($endDate)) {
			// Skip weekends (Saturday and Sunday)
			if ($currentDate->isWeekday()) {
				// Check if it's not a public holiday
				$dateString = $currentDate->format('Y-m-d');
				if (!in_array($dateString, $publicHolidays)) {
					$workingDays++;
				}
			}

			$currentDate->addDay();
		}

		return $workingDays;
	}

	/**
	 * Get South African public holidays for given years
	 * Uses cache to avoid recalculating holidays
	 *
	 * @param int $startYear
	 * @param int $endYear
	 * @return array
	 */
	public function getSouthAfricanPublicHolidays(int $startYear, int $endYear): array
	{
		$cacheKey = "sa_holidays_{$startYear}_{$endYear}";

		return Cache::remember($cacheKey, now()->addDays(30), function () use ($startYear, $endYear) {
			$holidays = [];

			for ($year = $startYear; $year <= $endYear; $year++) {
				// Fixed date holidays
				$holidays[] = "{$year}-01-01"; // New Year's Day
				$holidays[] = "{$year}-03-21"; // Human Rights Day
				$holidays[] = "{$year}-04-27"; // Freedom Day
				$holidays[] = "{$year}-05-01"; // Workers' Day
				$holidays[] = "{$year}-06-16"; // Youth Day
				$holidays[] = "{$year}-08-09"; // National Women's Day
				$holidays[] = "{$year}-09-24"; // Heritage Day
				$holidays[] = "{$year}-12-16"; // Day of Reconciliation
				$holidays[] = "{$year}-12-25"; // Christmas Day
				$holidays[] = "{$year}-12-26"; // Day of Goodwill

				// Calculate Easter holidays for the year
				$easterDate = $this->calculateEasterDate($year);
				$goodFriday = (clone $easterDate)->subDays(2)->format('Y-m-d');
				$easterMonday = (clone $easterDate)->addDay()->format('Y-m-d');

				$holidays[] = $goodFriday;    // Good Friday
				$holidays[] = $easterMonday;  // Family Day

				// Check for any observed holidays
				// (e.g., if a holiday falls on a Sunday, the following Monday might be observed)
				$this->addObservedHolidays($holidays, $year);
			}

			return $holidays;
		});
	}

	/**
	 * Add any observed holidays when fixed holidays fall on weekends
	 *
	 * @param array $holidays
	 * @param int $year
	 * @return void
	 */
	private function addObservedHolidays(array &$holidays, int $year): void
	{
		$fixedHolidays = [
				"{$year}-01-01", // New Year's Day
				"{$year}-03-21", // Human Rights Day
				"{$year}-04-27", // Freedom Day
				"{$year}-05-01", // Workers' Day
				"{$year}-06-16", // Youth Day
				"{$year}-08-09", // National Women's Day
				"{$year}-09-24", // Heritage Day
				"{$year}-12-16", // Day of Reconciliation
				"{$year}-12-25", // Christmas Day
				"{$year}-12-26", // Day of Goodwill
		];

		foreach ($fixedHolidays as $holiday) {
			$date = Carbon::parse($holiday);

			// If holiday falls on a Sunday, following Monday is observed
			if ($date->isSunday()) {
				$holidays[] = $date->copy()->addDay()->format('Y-m-d');
			}
		}
	}

	/**
	 * Calculate Easter date for a given year
	 * using Butcher's algorithm
	 *
	 * @param int $year
	 * @return Carbon
	 */
	private function calculateEasterDate(int $year): Carbon
	{
		$a = $year % 19;
		$b = floor($year / 100);
		$c = $year % 100;
		$d = floor($b / 4);
		$e = $b % 4;
		$f = floor(($b + 8) / 25);
		$g = floor(($b - $f + 1) / 3);
		$h = (19 * $a + $b - $d - $g + 15) % 30;
		$i = floor($c / 4);
		$k = $c % 4;
		$l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
		$m = floor(($a + 11 * $h + 22 * $l) / 451);
		$month = floor(($h + $l - 7 * $m + 114) / 31);
		$day = (($h + $l - 7 * $m + 114) % 31) + 1;

		return Carbon::createFromDate($year, $month, $day);
	}

	/**
	 * Get the financial year dates
	 *
	 * @param Carbon|null $date
	 * @return array
	 */
	public function getFinancialYearDates(?Carbon $date = null): array
	{
		$date = $date ?? now();
		$year = $date->year;

		// Financial year runs from April 1 to March 31
		if ($date->month < 4) {
			$startDate = Carbon::createFromDate($year - 1, 4, 1)->startOfDay();
			$endDate = Carbon::createFromDate($year, 3, 31)->endOfDay();
		} else {
			$startDate = Carbon::createFromDate($year, 4, 1)->startOfDay();
			$endDate = Carbon::createFromDate($year + 1, 3, 31)->endOfDay();
		}

		return [
				'start' => $startDate,
				'end' => $endDate,
		];
	}

	/**
	 * Calculate the duration in working days for a project
	 *
	 * @param Carbon $startDate
	 * @param Carbon $endDate
	 * @param bool $excludeHolidays
	 * @return int
	 */
	public function calculateProjectDuration(Carbon $startDate, Carbon $endDate, bool $excludeHolidays = true): int
	{
		if ($excludeHolidays) {
			return $this->calculateWorkingDays($startDate, $endDate);
		}

		// If not excluding holidays, just count weekdays
		$days = 0;
		$currentDate = $startDate->copy();

		while ($currentDate->lte($endDate)) {
			if ($currentDate->isWeekday()) {
				$days++;
			}
			$currentDate->addDay();
		}

		return $days;
	}
}