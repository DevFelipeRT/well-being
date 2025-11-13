<?php

declare(strict_types=1);

namespace App\Services\Demo;

use App\Models\CheckIn;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class DemoSeeder
{
    /**
     * Seeds up to 22 check-ins across the last 30 days for the given user.
     * Idempotent per user+day: skips existing days to allow "reset" without duplicates.
     */
    public function seed(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $today = CarbonImmutable::today();
            $created = 0;

            for ($i = 29; $i >= 0; $i--) {
                // Randomly skip some days to simulate usage
                if (random_int(0, 100) < 35) continue;

                $date = $today->subDays($i)->toDateString();

                $exists = CheckIn::query()
                    ->where('user_id', $user->getKey())
                    ->whereDate('checked_at', $date)
                    ->exists();

                if ($exists) continue;

                $score = random_int(2, 5);

                CheckIn::query()->create([
                    'user_id'    => $user->getKey(),
                    'checked_at' => $date,
                    'score'      => $score,
                    'note'       => $this->noteFor($score),
                ]);

                if (++$created >= 22) break;
            }
        });
    }

    public function reset(User $user): void
    {
        DB::transaction(function () use ($user): void {
            CheckIn::query()->where('user_id', $user->getKey())->delete();
        });

        $this->seed($user);
    }

    private function noteFor(int $score): ?string
    {
        return match (true) {
            $score >= 5 => 'Great energy and focus.',
            $score === 4 => 'Good day overall.',
            $score === 3 => 'Average mood, steady.',
            $score === 2 => 'Tired; kept it simple.',
            default      => 'Difficult day; taking it slow.',
        };
    }
}
