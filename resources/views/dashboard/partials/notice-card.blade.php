{{-- resources/views/dashboard/partials/notice-card.blade.php

Top notice / quick action card for the dashboard.

Inputs:
- \App\Models\CheckIn|null $todayCheckIn  Current day check-in or null.
- string|null              $viewUrl       URL to view today’s check-in when it exists.
- string|null              $editUrl       URL to edit today’s check-in when it exists.
- string|null              $createUrl     URL to create a new check-in when none exists.

Notes:
- Pure presentational partial. No domain or authorization logic.
- Self-contained styles; no global side effects.
--}}

@php
    /** @var \App\Models\CheckIn|null $todayCheckIn */
    $todayCheckIn = $todayCheckIn ?? null;
    $viewUrl  = $viewUrl  ?? null;
    $editUrl  = $editUrl  ?? null;
    $createUrl = $createUrl ?? null;
@endphp

<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-md rounded-md border-2 border-indigo-600 shadow-indigo-600 hover:shadow-indigo-600 hover:shadow-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100 flex flex-wrap items-center justify-between gap-4">
        <div class="text-sm sm:text-base">
            @if($todayCheckIn)
                <span class="font-medium">You've already checked in today.</span>
                <span class="text-gray-600 dark:text-gray-300">Score:</span>
                <span class="font-semibold">{{ $todayCheckIn->score }}</span>
            @else
                <h3 class="text-base font-bold">No check-in for today yet.</h3>
                <span class="text-gray-600 dark:text-gray-300">Record your well-being now.</span>
            @endif
        </div>

        <div class="flex justify-center items-center gap-2 grow  sm:grow-0">
            @if($todayCheckIn)
                @if($viewUrl)
                    <a
                        href="{{ $viewUrl }}"
                        class="inline-flex  justify-center items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        View
                    </a>
                @endif
                @if($editUrl)
                    <a
                        href="{{ $editUrl }}"
                        class="inline-flex  justify-center items-center rounded-md text-white bg-indigo-600 px-4 py-2 text-sm font-semibold hover:bg-indigo-700"
                    >
                        Edit today’s check-in
                    </a>
                @endif
            @else
                @if($createUrl)
                    <a
                        href="{{ $createUrl }}"
                        class="inline-flex w-full h-full max-w-xs justify-center items-center rounded-md text-white bg-indigo-600 px-4 py-2 text-sm font-semibold hover:bg-indigo-700"
                    >
                        New check-in
                    </a>
                @endif
            @endif
        </div>
    </div>
</div>
