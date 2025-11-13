<?php
// database/seeders/CheckInSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CheckIn;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;

final class CheckInSeeder extends Seeder
{
    /**
     * Seeds recent check-ins for the demo user (last 30 days).
     */
    public function run(): void
    {
        /** @var User|null $user */
        $user = User::query()->where('email', 'demo@wellbeing.test')->first();
        if (!$user) {
            $this->call(DemoUserSeeder::class);
            $user = User::query()->where('email', 'demo@wellbeing.test')->first();
        }
        if (!$user) {
            return;
        }

        $today = CarbonImmutable::today();

        // Create up to 22 check-ins across the last 30 days, skipping some days.
        $daysToSeed = 30;
        $created = 0;

        for ($i = $daysToSeed - 1; $i >= 0; $i--) {
            // Randomly skip some days to simulate real usage.
            if (random_int(0, 100) < 35) {
                continue;
            }

            $date = $today->subDays($i)->toDateString();

            // Avoid duplicates if seeder is run multiple times.
            $exists = CheckIn::query()
                ->where('user_id', $user->getKey())
                ->whereDate('checked_at', $date)
                ->exists();

            if ($exists) {
                continue;
            }

            $score = random_int(2, 5);

            CheckIn::query()->create([
                'user_id'    => $user->getKey(),
                'checked_at' => $date,
                'score'      => $score,
                'note'       => $this->noteFor($score),
            ]);

            $created++;
            if ($created >= 22) {
                break;
            }
        }
    }

    /**
     * Generates a short note aligned with the score.
     */
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
