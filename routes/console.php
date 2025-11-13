<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Carbon\CarbonImmutable;

// Registers a purge command without needing a Console Kernel class.
Artisan::command('demo:purge {--hours=12 : Delete demo users older than N hours}', function () {
    $hours = max(1, (int) $this->option('hours'));
    $threshold = CarbonImmutable::now()->subHours($hours);

    $users = DB::table('users')
        ->select('id')
        ->where('email', 'like', '%@wellbeing.demo')
        ->where('created_at', '<=', $threshold)
        ->get();

    if ($users->isEmpty()) {
        $this->info('No demo users to purge.');
        return;
    }

    $ids = $users->pluck('id')->all();

    DB::transaction(function () use ($ids) {
        DB::table('check_ins')->whereIn('user_id', $ids)->delete();
        DB::table('users')->whereIn('id', $ids)->delete();
    });

    $this->info(sprintf('Purged %d demo users.', count($ids)));
})->purpose('Deletes old demo users and their check-ins (email ends with @wellbeing.demo)');
