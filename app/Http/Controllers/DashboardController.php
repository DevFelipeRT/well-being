<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Dashboard\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Renders the well-being dashboard with summaries and recent activity.
 */
final class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboard,
    ) {}

    /**
     * Displays the dashboard overview for the authenticated user.
     */
    public function index(): View
    {
        $userId = (int) Auth::id();

        $overview = $this->dashboard->buildOverview($userId, recentLimit: 5);

        return view('dashboard.index', $overview);
    }
}
