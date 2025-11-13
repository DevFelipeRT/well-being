<?php
// app/Http/Controllers/DemoController.php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\Demo\DemoSeeder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

final class DemoController extends Controller
{
    public function __construct(
        private readonly DemoSeeder $demoSeeder,
    ) {}

    /**
     * Starts (or resumes) a demo session:
     * - Creates a temporary user bound to the current session if needed.
     * - Seeds demo data for that user.
     * - Logs in the demo user and redirects to the dashboard.
     */
    public function start(): RedirectResponse
    {
        $userId = session('demo_user_id');
        $user = $userId ? User::query()->find($userId) : null;

        if (!$user) {
            $user = $this->createEphemeralUser();
            session(['demo_user_id' => $user->getKey()]);
        }

        $this->demoSeeder->seed($user);

        Auth::login($user, false);

        return redirect()->route('dashboard')
            ->with('info', 'Demo session initialized.');
    }

    /**
     * Resets the current demo session data (keeps the same user).
     */
    public function reset(): RedirectResponse
    {
        $userId = session('demo_user_id');
        $user = $userId ? User::query()->find($userId) : null;

        if (!$user) {
            return redirect()->route('demo.start')->with('warning', 'Demo user not found. Starting a new session.');
        }

        $this->demoSeeder->reset($user);

        return redirect()->route('dashboard')
            ->with('success', 'Demo data reset successfully.');
    }

    /**
     * Ends the demo session (logout and clear session binding).
     */
    public function end(): RedirectResponse
    {
        session()->forget('demo_user_id');
        Auth::logout();

        return redirect()->route('login')->with('info', 'Demo session ended.');
    }

    private function createEphemeralUser(): User
    {
        $token = Str::lower(Str::ulid());
        $email = "demo+{$token}@wellbeing.demo";

        /** @var User $user */
        $user = User::query()->create([
            'name'              => "Demo User {$token}",
            'email'             => $email,
            'email_verified_at' => now(),
            // Never reused; random hash is fine for ephemeral users.
            'password'          => bcrypt(Str::random(32)),
        ]);

        return $user;
    }
}
