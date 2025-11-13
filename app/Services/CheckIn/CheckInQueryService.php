<?php

declare(strict_types=1);

namespace App\Services\CheckIn;

use App\Models\CheckIn;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * Read-side queries for well-being check-ins.
 * Exposes only structured data without UI phrasing or formatting.
 */
final class CheckInQueryService
{
    /**
     * Returns a paginated list of check-ins for a user, newest first.
     * The $from and $to parameters are inclusive calendar days; null means unbounded.
     */
    public function paginateForUser(
        int $userId,
        ?CarbonInterface $from = null,
        ?CarbonInterface $to = null,
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = CheckIn::query()
            ->forUser($userId)
            ->ordered();

        if ($from !== null) {
            $query->whereDate('checked_at', '>=', $from->toDateString());
        }

        if ($to !== null) {
            $query->whereDate('checked_at', '<=', $to->toDateString());
        }

        /** @var LengthAwarePaginator $paginator */
        $paginator = $query->paginate($perPage)->withQueryString();

        return $paginator;
    }

    /**
     * Returns all check-ins for a user within an inclusive interval, newest first.
     * Intended for lightweight exports or charts when pagination is not required.
     */
    public function listForUser(
        int $userId,
        ?CarbonInterface $from = null,
        ?CarbonInterface $to = null
    ): Collection {
        $query = CheckIn::query()
            ->forUser($userId)
            ->ordered();

        if ($from !== null) {
            $query->whereDate('checked_at', '>=', $from->toDateString());
        }

        if ($to !== null) {
            $query->whereDate('checked_at', '<=', $to->toDateString());
        }

        /** @var Collection $items */
        $items = $query->get();

        return $items;
    }

    /**
     * Computes aggregate metrics for a user in an inclusive date interval.
     *
     * Returns:
     * - count: int
     * - average_score: float|null
     * - min_score: int|null
     * - max_score: int|null
     * - first_date: string|null (Y-m-d)
     * - last_date: string|null  (Y-m-d)
     * - best_day: array|null    ['date' => 'Y-m-d', 'score' => int]
     * - worst_day: array|null   ['date' => 'Y-m-d', 'score' => int]
     */
    public function summary(
        int $userId,
        CarbonInterface $from,
        CarbonInterface $to
    ): array {
        $fromDate = $from->toDateString();
        $toDate   = $to->toDateString();

        $base = CheckIn::query()
            ->forUser($userId)
            ->whereDate('checked_at', '>=', $fromDate)
            ->whereDate('checked_at', '<=', $toDate);

        $count = (clone $base)->count();

        if ($count === 0) {
            return [
                'count'         => 0,
                'average_score' => null,
                'min_score'     => null,
                'max_score'     => null,
                'first_date'    => null,
                'last_date'     => null,
                'best_day'      => null,
                'worst_day'     => null,
            ];
        }

        $avg = (float) (clone $base)->avg('score');
        $min = (int) (clone $base)->min('score');
        $max = (int) (clone $base)->max('score');

        $first = (clone $base)
            ->orderBy('checked_at', 'asc')
            ->orderBy('id', 'asc')
            ->value('checked_at');

        $last = (clone $base)
            ->orderBy('checked_at', 'desc')
            ->orderBy('id', 'desc')
            ->value('checked_at');

        $best = (clone $base)
            ->orderBy('score', 'desc')
            ->orderBy('checked_at', 'desc')
            ->orderBy('id', 'desc')
            ->first(['checked_at', 'score']);

        $worst = (clone $base)
            ->orderBy('score', 'asc')
            ->orderBy('checked_at', 'asc')
            ->orderBy('id', 'asc')
            ->first(['checked_at', 'score']);

        return [
            'count'         => $count,
            'average_score' => $avg,
            'min_score'     => $min,
            'max_score'     => $max,
            'first_date'    => $first?->format('Y-m-d'),
            'last_date'     => $last?->format('Y-m-d'),
            'best_day'      => $best ? ['date' => $best->checked_at->toDateString(), 'score' => (int) $best->score] : null,
            'worst_day'     => $worst ? ['date' => $worst->checked_at->toDateString(), 'score' => (int) $worst->score] : null,
        ];
    }

    /**
     * Convenience summary for the last N days, inclusive of today.
     */
    public function summaryLastDays(int $userId, int $days): array
    {
        $to = CarbonImmutable::today();
        $from = $to->subDays(max(0, $days - 1));

        return $this->summary($userId, $from, $to);
    }

    /**
     * Convenience summary for the current calendar month.
     */
    public function summaryThisMonth(int $userId): array
    {
        $today = CarbonImmutable::today();

        $from = $today->startOfMonth();
        $to   = $today->endOfMonth();

        return $this->summary($userId, $from, $to);
    }

    /**
     * Builds an overall analysis for the dashboard using structured data only.
     * Compares a recent window against an immediately preceding window of equal length.
     *
     * Returns:
     * - period_recent: array{from:string,to:string,days:int}
     * - period_previous: array{from:string,to:string,days:int}
     * - avg_recent: float|null
     * - avg_previous: float|null
     * - trend: array{label:'improving'|'declining'|'stable', delta_pct: float}
     * - recent_count: int
     * - previous_count: int
     * - weekday_distribution: array<string,int> // Mon..Sun => 0..100
     * - weekday_extremes: array{best:?string,worst:?string}
     * - good_cutoff: array{value:int,strategy:'fixed'|'adaptive',percentile?:float}
     */
    public function overall(
        int $userId,
        int $recentDays = 7,
        int $weekdayLookbackDays = 90,
        ?int $goodScoreCutoff = null,
        float $adaptivePercentile = 0.60
    ): array {
        $recentDays = max(1, $recentDays);

        $today        = CarbonImmutable::today();
        $recentFrom   = $today->subDays($recentDays - 1);
        $previousTo   = $recentFrom->subDay();
        $previousFrom = $previousTo->subDays($recentDays - 1);

        $recent   = $this->summary($userId, $recentFrom, $today);
        $previous = $this->summary($userId, $previousFrom, $previousTo);

        $avgRecent   = $recent['average_score'];
        $avgPrevious = $previous['average_score'];

        $trendLabel = 'stable';
        $trendDelta = 0.0;

        if ($avgRecent !== null && $avgPrevious !== null) {
            $den = max(1e-9, (float) $avgPrevious);
            $trendDelta = (($avgRecent - $avgPrevious) / $den) * 100.0;

            if (abs($trendDelta) < 3.0) {
                $trendLabel = 'stable';
            } elseif ($trendDelta > 0.0) {
                $trendLabel = 'improving';
            } else {
                $trendLabel = 'declining';
            }
        }

        $lookbackFrom = $today->subDays(max(1, $weekdayLookbackDays) - 1);

        $distributionResult = $this->weekdayDistribution(
            $userId,
            $lookbackFrom,
            $today,
            $goodScoreCutoff,
            $adaptivePercentile
        );

        $percentages = $distributionResult['percentages'];  // Mon..Sun => int|null
        $totals      = $distributionResult['totals'];       // Mon..Sun => int
        $hasData     = $distributionResult['has_data'];     // bool

        $bestWeekday  = null;
        $worstWeekday = null;

        if ($hasData) {
            $bestWeekday  = $this->pickExtremeWeekdayByPct($percentages, $totals, true);
            $worstWeekday = $this->pickExtremeWeekdayByPct($percentages, $totals, false);
        }

        return [
            'period_recent' => [
                'from' => $recentFrom->toDateString(),
                'to'   => $today->toDateString(),
                'days' => $recentDays,
            ],
            'period_previous' => [
                'from' => $previousFrom->toDateString(),
                'to'   => $previousTo->toDateString(),
                'days' => $recentDays,
            ],
            'avg_recent'           => $avgRecent,
            'avg_previous'         => $avgPrevious,
            'trend'                => ['label' => $trendLabel, 'delta_pct' => $trendDelta],
            'recent_count'         => (int) ($recent['count'] ?? 0),
            'previous_count'       => (int) ($previous['count'] ?? 0),

            'weekday_distribution' => $percentages,
            'weekday_totals'       => $totals,
            'weekday_extremes'     => ['best' => $bestWeekday, 'worst' => $worstWeekday],
            'weekday_has_data'     => $hasData,
        ];
    }

    /**
    * Computes "good day" rate per weekday within [from..to] using a fixed or adaptive cutoff.
    * Days with no observations are marked as null (not 0) and excluded from extremes.
    *
    * @return array{
    *   percentages: array<string, int|null>,   // Mon..Sun => 0..100 or null when count==0
    *   totals: array<string, int>,             // Mon..Sun => total observations
    *   has_data: bool,                         // true when sum(totals) > 0
    *   cutoff: int
    * }
    */
    private function weekdayDistribution(
        int $userId,
        CarbonInterface $from,
        CarbonInterface $to,
        ?int $goodScoreCutoff,
        float $adaptivePercentile
    ): array {
        $fromDate = $from->toDateString();
        $toDate   = $to->toDateString();

        $rows = CheckIn::query()
            ->forUser($userId)
            ->whereDate('checked_at', '>=', $fromDate)
            ->whereDate('checked_at', '<=', $toDate)
            ->orderBy('checked_at', 'asc')
            ->get(['checked_at', 'score']);

        $totals = [
            'Mon' => 0, 'Tue' => 0, 'Wed' => 0, 'Thu' => 0,
            'Fri' => 0, 'Sat' => 0, 'Sun' => 0,
        ];
        $goods = [
            'Mon' => 0, 'Tue' => 0, 'Wed' => 0, 'Thu' => 0,
            'Fri' => 0, 'Sat' => 0, 'Sun' => 0,
        ];

        if ($rows->isEmpty()) {
            return [
                'percentages' => [
                    'Mon' => null, 'Tue' => null, 'Wed' => null, 'Thu' => null,
                    'Fri' => null, 'Sat' => null, 'Sun' => null,
                ],
                'totals'   => $totals,
                'has_data' => false,
                'cutoff'   => $goodScoreCutoff ?? 7,
            ];
        }

        $scores = $rows->pluck('score')->map(static fn ($s) => (int) $s)->values()->all();

        $cutoff = $goodScoreCutoff;
        if ($cutoff === null) {
            $cutoff = $this->percentile($scores, $adaptivePercentile) ?? 7;
        }

        foreach ($rows as $r) {
            /** @var \App\Models\CheckIn $r */
            $weekday = $this->weekdayShort($r->checked_at);
            if (!isset($totals[$weekday])) {
                continue;
            }
            $totals[$weekday]++;
            if ((int) $r->score >= $cutoff) {
                $goods[$weekday]++;
            }
        }

        $percentages = [];
        $sumTotals = 0;

        foreach ($totals as $k => $total) {
            $sumTotals += $total;
            if ($total <= 0) {
                $percentages[$k] = null; // sem observação => dado inexistente
            } else {
                $pct = (int) round(($goods[$k] / $total) * 100, 0);
                $percentages[$k] = $pct;
            }
        }

        return [
            'percentages' => $percentages,
            'totals'      => $totals,
            'has_data'    => $sumTotals > 0,
            'cutoff'      => (int) $cutoff,
        ];
    }

    /**
     * Returns "Mon".."Sun" for a given date.
     */
    private function weekdayShort(CarbonInterface $date): string
    {
        $iso = (int) $date->isoWeekday(); // 1 = Mon .. 7 = Sun
        return match ($iso) {
            1 => 'Mon',
            2 => 'Tue',
            3 => 'Wed',
            4 => 'Thu',
            5 => 'Fri',
            6 => 'Sat',
            7 => 'Sun',
            default => 'Mon',
        };
    }

    /**
    * Picks best/worst weekday by percentage; considers only weekdays with total > 0 and non-null percentage.
    * Tie-break: higher total first, then Mon..Sun order.
    */
    private function pickExtremeWeekdayByPct(array $percentages, array $totals, bool $pickMax): ?string
    {
        $order = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];

        $candidate = null;
        $bestPct   = $pickMax ? -INF : INF;
        $bestCnt   = -1;

        foreach ($order as $day) {
            $pct = $percentages[$day] ?? null;
            $cnt = (int) ($totals[$day] ?? 0);

            if ($pct === null || $cnt <= 0) {
                continue;
            }

            if ($pickMax) {
                if ($pct > $bestPct || ($pct === $bestPct && $cnt > $bestCnt)) {
                    $bestPct = $pct;
                    $bestCnt = $cnt;
                    $candidate = $day;
                }
            } else {
                if ($pct < $bestPct || ($pct === $bestPct && $cnt > $bestCnt)) {
                    $bestPct = $pct;
                    $bestCnt = $cnt;
                    $candidate = $day;
                }
            }
        }

        return $candidate;
    }

    /**
     * Computes the given percentile over integer scores using the nearest-rank method.
     * Returns null if the input is empty.
     */
    private function percentile(array $values, float $p): ?int
    {
        $n = count($values);
        if ($n === 0) {
            return null;
        }

        $p = min(1.0, max(0.0, $p));
        sort($values, SORT_NUMERIC);

        $rank = (int) ceil($p * $n);
        $rank = max(1, min($rank, $n));

        return (int) $values[$rank - 1];
    }

    /**
     * Returns an empty Mon..Sun map with zero percentages.
     */
    private function emptyWeekdayMap(): array
    {
        return [
            'Mon' => 0,
            'Tue' => 0,
            'Wed' => 0,
            'Thu' => 0,
            'Fri' => 0,
            'Sat' => 0,
            'Sun' => 0,
        ];
    }
}
