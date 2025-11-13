<?php

declare(strict_types=1);

namespace App\Services\Dashboard;

use App\Models\CheckIn;
use App\Services\CheckIn\CheckInQueryService;
use App\Services\CheckIn\CheckInService;
use Illuminate\Support\Collection;

/**
 * Aggregates data required by the dashboard in a single call.
 * Keeps orchestration and light formatting here; heavy calculations stay in query services.
 */
final class DashboardService
{
    public function __construct(
        private readonly CheckInService $checkIns,
        private readonly CheckInQueryService $queries,
    ) {}

    /**
     * Builds the overview payload for the dashboard.
     *
     * @param  int  $userId
     * @param  int  $recentLimit
     * @return array{
     *     todayCheckIn: \App\Models\CheckIn|null,
     *     summary7: array,
     *     summary30: array,
     *     summaryMonth: array,
     *     recentItems: \Illuminate\Support\Collection<int,\App\Models\CheckIn>,
     *     overall: array
     * }
     */
    public function buildOverview(int $userId, int $recentLimit = 5): array
    {
        $todayCheckIn = $this->checkIns->findToday($userId);

        $summary7     = $this->queries->summaryLastDays($userId, 7);
        $summary30    = $this->queries->summaryLastDays($userId, 30);
        $summaryMonth = $this->queries->summaryThisMonth($userId);

        $overall = $this->queries->overall(
            userId: $userId,
            recentDays: 7,
            weekdayLookbackDays: 90,
            goodScoreCutoff: null,
            adaptivePercentile: 0.60,
        );

        return [
            'todayCheckIn' => $todayCheckIn,
            'summary7'     => $summary7,
            'summary30'    => $summary30,
            'summaryMonth' => $summaryMonth,
            'recentItems'  => $this->recentForUser($userId, $recentLimit),
            'overall'      => $overall,
        ];
    }

    /**
     * Returns the most recent check-ins for the user.
     *
     * @param  int  $userId
     * @param  int  $limit
     * @return \Illuminate\Support\Collection<int,\App\Models\CheckIn>
     */
    public function recentForUser(int $userId, int $limit = 5): Collection
    {
        $limit = max(1, min(50, $limit));

        return CheckIn::query()
            ->where('user_id', $userId)
            ->orderByDesc('checked_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
        }
}
