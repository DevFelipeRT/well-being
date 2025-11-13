{{-- resources/views/check-ins/index.blade.php

Check-ins index page (decomposed into partials).

Expects:
- array $summary7
- array $summary30
- array $filters
- \Illuminate\Contracts\Pagination\LengthAwarePaginator $items
--}}

@php
    // Centralized button styles (shared across partials via includes' parameters).
    $btnPrimary   = 'inline-flex min-h-[38px] justify-center items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-pretty';
    $btnSecondary = 'inline-flex min-h-[38px] justify-center items-center rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400';
    $btnDanger    = 'inline-flex min-h-[38px] justify-center items-center rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500';
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">Check-in History</h2>
    </x-slot>

    <div class="mx-auto max-w-7xl space-y-6">
        <div class="hidden sm:block">
            <h3 class="w-full px-1 py-2 mb-6 border-b border-gray-800/40 dark:border-white/40 ">
                <span class="font-semibold text-xl text-gray-800 dark:text-gray-200">Score averages</span>
            </h3>
            {{-- Summary --}}
            @include('check-ins.partials.summary-row', [
                'summary7'  => $summary7 ?? [],
                'summary30' => $summary30 ?? [],
                'summaryMonth' => $summaryMonth ?? [],
                'btnPrimary'=> $btnPrimary,
            ])
        </div>

        <div class="flex flex-col gap-6">
            <h3 class="hidden sm:block w-full px-1 py-2 border-b border-gray-800/40 dark:border-white/40 ">
                <span class="font-semibold text-xl text-gray-800 dark:text-gray-200">History</span>
            </h3>

            <div>
                {{-- Filters --}}
                @include('check-ins.partials.filters-form', [
                    'action'      => route('check-ins.index'),
                    'filters'     => $filters ?? [],
                    'btnPrimary'  => $btnPrimary,
                    'btnSecondary'=> $btnSecondary,
                ])
            </div>

            <div>
                {{-- Table --}}
                @include('check-ins.partials.table', [
                    'items'       => $items,
                    'btnPrimary'  => $btnPrimary,
                    'btnSecondary'=> $btnSecondary,
                    'btnDanger'   => $btnDanger,
                ])
            </div>
        </div>
    </div>
</x-app-layout>
