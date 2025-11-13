{{-- resources/views/check-ins/show.blade.php

Check-in details page with a safe back link that returns to the previous page
when available, falling back to the index route.
--}}

@php
    $btnPri = 'inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500';
    $btnSec = 'inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700';

    // Compute a safe back URL (same host, not current URL) with fallback to index
    $previous  = url()->previous();
    $current   = url()->current();
    $prevHost  = parse_url($previous ?? '', PHP_URL_HOST);
    $safeHost  = request()->getHost();

    $backUrl = ($previous && $previous !== $current && $prevHost === $safeHost)
        ? $previous
        : route('check-ins.index');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Check-in Details
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8 space-y-6">

            {{-- Details card (extracted partial) --}}
            @include('check-ins.partials.details-card', ['checkIn' => $checkIn])

            {{-- Bottom actions --}}
            <div class="flex items-center justify-between">
                <a href="{{ $backUrl }}" class="{{ $btnSec }}">Back</a>

                <div class="flex items-center gap-2">
                    @can('update', $checkIn)
                        <a href="{{ route('check-ins.edit', $checkIn) }}" class="{{ $btnPri }}">Edit</a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
