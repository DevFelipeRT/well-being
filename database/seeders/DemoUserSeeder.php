<?php
// database/seeders/DemoUserSeeder.php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DemoUserSeeder extends Seeder
{
    /**
     * Creates a demo user if it does not exist.
     */
    public function run(): void
    {
        $email = 'demo@wellbeing.test';

        $user = User::query()->where('email', $email)->first();
        if ($user) {
            return;
        }

        User::factory()->create([
            'name'              => 'Demo User',
            'email'             => $email,
            'email_verified_at' => now(),
            'password'          => Hash::make('password'),
        ]);
    }
}
