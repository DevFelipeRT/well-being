<?php
// database/migrations/2025_11_11_000000_create_check_ins_table.php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Creates the check_ins table.
     */
    public function up(): void
    {
        Schema::create('check_ins', function (Blueprint $table): void {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('checked_at')->index();

            $table->unsignedTinyInteger('score'); // 1..5

            $table->text('note')->nullable();

            $table->timestamps();

            // Enforces one check-in per user per calendar day.
            $table->unique(['user_id', 'checked_at'], 'check_ins_user_day_unique');
        });

        // Optional CHECK constraint (score between 1 and 5) where supported.
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // MySQL 8.0.16+ supports CHECK constraints
            DB::statement('ALTER TABLE `check_ins` ADD CONSTRAINT `check_ins_score_range` CHECK ((`score` BETWEEN 1 AND 5))');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE check_ins ADD CONSTRAINT check_ins_score_range CHECK (score BETWEEN 1 AND 5)');
        }
        // SQLite: no explicit CHECK here (or would need raw SQL at create-time).
    }

    /**
     * Drops the check_ins table.
     */
    public function down(): void
    {
        // Drop CHECK constraint explicitly on engines that support it.
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            // MySQL 8.0.16+: DROP CHECK syntax
            DB::statement('ALTER TABLE `check_ins` DROP CHECK `check_ins_score_range`');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE check_ins DROP CONSTRAINT IF EXISTS check_ins_score_range');
        }

        Schema::dropIfExists('check_ins');
    }
};
