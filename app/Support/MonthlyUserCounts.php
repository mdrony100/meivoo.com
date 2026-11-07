<?php

namespace App\Support;

use App\Models\User;

class MonthlyUserCounts
{
    /**
     * Signups per calendar month for a year, indexed 0..11 for January..December.
     *
     * MySQL is the supported driver. MONTH() is evaluated in the database.
     *
     * @return list<int>
     */
    public static function forYear(int $year): array
    {
        $raw = User::query()
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->whereYear('created_at', $year)
            ->groupByRaw('MONTH(created_at)')
            ->pluck('total', 'month');

        $chart = [];

        for ($month = 1; $month <= 12; $month++) {
            $chart[] = (int) ($raw[$month] ?? $raw[(string) $month] ?? 0);
        }

        return $chart;
    }
}
