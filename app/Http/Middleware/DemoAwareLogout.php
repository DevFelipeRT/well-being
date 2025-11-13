<?php
// app/Http/Middleware/DemoAwareLogout.php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Converts a standard POST /logout into a demo-aware logout when the session
 * holds a demo user marker. It short-circuits the pipeline to ensure a clean
 * end-of-demo flow and a clear redirect with feedback.
 */
final class DemoAwareLogout
{
    public function handle(Request $request, Closure $next): Response
    {
        $isLogout = $request->isMethod('post') && $request->routeIs('logout');
        $isDemo   = $request->session()->has('demo_user_id');

        if (! $isLogout || ! $isDemo) {
            return $next($request);
        }

        // Perform an explicit "demo end" here to avoid hitting the default handler twice.
        $request->session()->forget('demo_user_id');

        // Complete logout cycle.
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Clear feedback toast and redirect to login.
        return redirect()
            ->route('login')
            ->with('info', 'Demo session ended.');
    }
}
