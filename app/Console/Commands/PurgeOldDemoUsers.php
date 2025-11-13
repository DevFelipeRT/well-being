<?php
// app/Console/Commands/PurgeOldDemoUsers.php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class PurgeOldDemoUsers extends Command
{
    protected $signature = 'demo:purge {--hours=12 : Delete demo users older than N hours}';
    protected $description = 'Deletes old demo users and their check-ins (email ends with @wellbeing.demo)';

    public function handle(): int
    {
        $hours = max(1, (int) $this->option('hours'));
        $threshold = CarbonImmutable::now()->subHours($hours);

        $users = User::query()
            ->where('email', 'like', '%@wellbeing.demo')
            ->where('created_at', '<=', $threshold)
            ->get(['id']);

        if ($users->isEmpty()) {
            $this->info('No demo users to purge.');
            return self::SUCCESS;
        }

        $ids = $users->pluck('id')->all();

        DB::transaction(function () use ($ids): void {
            DB::table('check_ins')->whereIn('user_id', $ids)->delete();
            DB::table('users')->whereIn('id', $ids)->delete();
        });

        $this->info(sprintf('Purged %d demo users.', count($ids)));
        return self::SUCCESS;
    }
}
