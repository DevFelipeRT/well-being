<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CheckIn\IndexCheckInRequest;
use App\Http\Requests\CheckIn\StoreCheckInRequest;
use App\Http\Requests\CheckIn\UpdateCheckInRequest;
use App\Mappers\CheckInMapper;
use App\Models\CheckIn;
use App\Services\CheckIn\CheckInQueryService;
use App\Services\CheckIn\CheckInService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * HTTP controller for well-being check-ins.
 * Handles listing, creation, edition, visualization and deletion flows.
 */
final class CheckInController extends Controller
{
    public function __construct(
        private readonly CheckInService $service,
        private readonly CheckInQueryService $queries,
    ) {}

    /**
     * Displays a paginated list of check-ins for the authenticated user.
     */
    public function index(IndexCheckInRequest $request): View
    {
        $userId  = Auth::id();

        $from = $request->validated('from');
        $to   = $request->validated('to');

        $fromDate = $from ? CarbonImmutable::createFromFormat('Y-m-d', $from) : null;
        $toDate   = $to ? CarbonImmutable::createFromFormat('Y-m-d', $to) : null;

        $perPage = (int) ($request->validated('per_page') ?? 15);

        $paginator = $this->queries->paginateForUser(
            userId: $userId,
            from: $fromDate,
            to: $toDate,
            perPage: $perPage
        );

        return view('check-ins.index', [
            'items'    => $paginator,
            'filters'  => ['from' => $from, 'to' => $to, 'per_page' => $perPage],
            'summary7' => $this->queries->summaryLastDays($userId, 7),
            'summary30'=> $this->queries->summaryLastDays($userId, 30),
            'summaryMonth'=> $this->queries->summaryThisMonth($userId),
        ]);
    }

    /**
     * Shows the creation form. If today's check-in already exists, redirects.
     */
    public function create(): RedirectResponse|View
    {
        $userId = Auth::id();

        $today = $this->service->findToday($userId);
        if ($today) {
            return redirect()
                ->route('check-ins.edit', $today)
                ->with('info', 'You have already created today’s check-in.');
        }

        return view('check-ins.create', [
            'defaultDate' => CarbonImmutable::today()->toDateString(),
        ]);
    }

    /**
     * Persists a new check-in for the authenticated user.
     */
    public function store(StoreCheckInRequest $request): RedirectResponse
    {
        $userId = Auth::id();

        $dto = CheckInMapper::toDto($request->validated());

        $checkIn = $this->service->createForUser($userId, $dto);

        $detailsHtml = view('check-ins.partials.details-card', [
            'checkIn' => $checkIn,
        ])->render();

        return redirect()
            ->route('dashboard')
            ->with('recent_details_html', $detailsHtml)
            ->with('success', 'Check-in created successfully.');
    }

    /**
     * Displays a single check-in.
     */
    public function show(CheckIn $checkIn): View
    {
        $this->authorize('view', $checkIn);

        return view('check-ins.show', [
            'checkIn' => $checkIn,
        ]);
    }

    /**
     * Shows the edit form for a single check-in.
     */
    public function edit(CheckIn $checkIn): View
    {
        $this->authorize('update', $checkIn);

        return view('check-ins.edit', [
            'checkIn' => $checkIn,
        ]);
    }

    /**
     * Updates a single check-in.
     */
    public function update(UpdateCheckInRequest $request, CheckIn $checkIn): RedirectResponse
    {
        $this->authorize('update', $checkIn);

        $userId = Auth::id();

        $dto = CheckInMapper::toDto($request->validated());

        $updated = $this->service->updateForUser(
            userId: $userId,
            checkInId: $checkIn->getKey(),
            data: $dto
        );

        $detailsHtml = view('check-ins.partials.details-card', [
            'checkIn' => $updated,
        ])->render();

        return redirect()
            ->route('dashboard')
            ->with('recent_details_html', $detailsHtml)
            ->with('success', 'Check-in updated successfully.');
    }

    /**
     * Deletes a single check-in.
     */
    public function destroy(CheckIn $checkIn): RedirectResponse
    {
        $this->authorize('delete', $checkIn);

        $userId = Auth::id();

        $this->service->deleteForUser($userId, $checkIn->getKey());

        return redirect()
            ->route('check-ins.index')
            ->with('success', 'Check-in deleted successfully.');
    }
}
