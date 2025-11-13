{{-- resources/views/check-ins/partials/form.blade.php

Reusable form for creating and editing check-ins.

Parameters:
- string                 $action
- string                 $method         'POST' or 'PUT'
- string                 $submitLabel
- \App\Models\CheckIn|null $checkIn
- string|null            $defaultDate    Y-m-d
- string|null            $cancelHref     Optional explicit cancel URL
- string                 $cancelLabel    Button label (default 'Cancel')
--}}

@props([
    'action',
    'method' => 'POST',
    'submitLabel' => 'Save',
    'checkIn' => null,
    'defaultDate' => null,
    'cancelHref' => null,
    'cancelLabel' => 'Cancel',
])

@php
    $isEdit     = strtoupper($method) === 'PUT';
    $dateValue  = old('checked_at', $isEdit ? optional($checkIn->checked_at)->toDateString() : ($defaultDate ?? now()->toDateString()));
    $scoreValue = (int) old('score', $isEdit ? (int) ($checkIn->score ?? 3) : 3);
    $noteValue  = old('note', $isEdit ? ($checkIn->note ?? null) : null);

    // Styles
    $label  = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $input  = 'mt-1 block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500';
    $hint   = 'mt-1 text-xs text-gray-500 dark:text-gray-400';
    $error  = 'mt-1 text-xs text-rose-600';
    $btnPri = 'inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500';
    $btnSec = 'inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700';

    // Safe cancel/back URL with fallback
    $previous  = $cancelHref ?: url()->previous();
    $current   = url()->current();
    $prevHost  = parse_url($previous ?? '', PHP_URL_HOST);
    $safeHost  = request()->getHost();
    $fallback  = $isEdit && $checkIn ? route('check-ins.show', $checkIn) : route('check-ins.index');

    $cancelUrl = ($previous && $previous !== $current && $prevHost === $safeHost) ? $previous : $fallback;
@endphp

<form action="{{ $action }}" method="POST" class="space-y-6">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div>
        <label for="checked_at" class="{{ $label }}">Date</label>
        <input id="checked_at" name="checked_at" type="date" value="{{ $dateValue }}" required class="{{ $input }}" />
        <p class="{{ $hint }}">Select the calendar day for this entry.</p>
        @error('checked_at') <p class="{{ $error }}">{{ $message }}</p> @enderror
    </div>

    <div x-data="{ score: {{ $scoreValue }} }" class="space-y-2">
        <span class="{{ $label }}">Well-being score</span>
        <div class="grid grid-cols-5 gap-2">
            @for($i = 1; $i <= 5; $i++)
                <label
                    class="flex cursor-pointer items-center justify-center rounded-md border px-3 py-2 text-sm font-medium transition
                           border-gray-300 dark:border-gray-600
                           text-gray-700 dark:text-gray-200
                           data-[active=true]:border-indigo-600 data-[active=true]:bg-indigo-50 dark:data-[active=true]:bg-indigo-900/30 data-[active=true]:text-indigo-700 dark:data-[active=true]:text-indigo-300"
                    :data-active="score >= {{ $i }}"
                >
                    <input type="radio" name="score" value="{{ $i }}" class="sr-only" @checked($scoreValue === $i) @change="score = {{ $i }}" />
                    {{ $i }}
                </label>
            @endfor
        </div>
        <p class="{{ $hint }}">From 1 (very low) to 5 (very high).</p>
        @error('score') <p class="{{ $error }}">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="note" class="{{ $label }}">Notes (optional)</label>
        <textarea id="note" name="note" rows="4" class="{{ $input }}" placeholder="Short context for today…">{{ $noteValue }}</textarea>
        @error('note') <p class="{{ $error }}">{{ $message }}</p> @enderror
    </div>

    <div class="flex items-center justify-between gap-3">
        <a href="{{ $cancelUrl }}" class="{{ $btnSec }}">{{ $cancelLabel }}</a>
        <button type="submit" class="{{ $btnPri }}">{{ $submitLabel }}</button>
    </div>
</form>
