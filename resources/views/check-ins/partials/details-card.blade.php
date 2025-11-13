{{-- resources/views/check-ins/partials/details-card.blade.php

Compact details card for a single Check-in.
Inputs:
- \App\Models\CheckIn $checkIn
--}}

@php
    /** @var \App\Models\CheckIn $checkIn */
    $dateChecked = optional($checkIn->checked_at)->toDateString() ?? '—';
    $score       = $checkIn->score ?? '—';
    $note        = $checkIn->note ?? '—';
    $createdAt   = optional($checkIn->created_at)->format('Y-m-d H:i') ?? '—';
    $updatedAt   = optional($checkIn->updated_at)->format('Y-m-d H:i') ?? '—';
@endphp

<div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
            {{ $dateChecked }}
        </h3>
    </div>

    <dl class="divide-y divide-gray-200 dark:divide-gray-700">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 px-6 py-4">
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Score</dt>
            <dd class="col-span-2 text-sm text-gray-900 dark:text-gray-100">{{ $score }}</dd>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 px-6 py-4">
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Notes</dt>
            <dd class="col-span-2 text-start text-sm text-gray-900 dark:text-gray-100">
                {{ $note }}
            </dd>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 px-6 py-4">
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created at</dt>
            <dd class="col-span-2 text-sm text-gray-900 dark:text-gray-100">{{ $createdAt }}</dd>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 px-6 py-4">
            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Updated at</dt>
            <dd class="col-span-2 text-sm text-gray-900 dark:text-gray-100">{{ $updatedAt }}</dd>
        </div>
    </dl>
</div>
