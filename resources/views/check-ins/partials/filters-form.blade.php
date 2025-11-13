{{-- resources/views/check-ins/partials/filters-form.blade.php

Filter form for check-ins list.

Inputs:
- string $action
- array  $filters            ['from' => ?, 'to' => ?, 'per_page' => ?]
- string $btnPrimary
- string $btnSecondary
--}}

@php
    $action      = $action ?? '#';
    $filters     = is_array($filters ?? null) ? $filters : [];
    $from        = $filters['from'] ?? '';
    $to          = $filters['to'] ?? '';
    $perPage     = (int) ($filters['per_page'] ?? 15);
    $perPageOpts = [10, 15, 25, 50, 100];
@endphp

<div class="rounded-lg bg-white dark:bg-gray-800 p-4 shadow">
    <form method="GET" action="{{ $action }}" class="grid grid-cols-1 gap-4 sm:grid-cols-5 sm:items-end">
        {{-- From --}}
        <div class="min-w-0">
            <label for="from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From</label>

            <div x-data="{ v: @js($from) }" class="relative mt-1">
                <input
                    x-model="v"
                    type="date"
                    id="from"
                    name="from"
                    value="{{ $from }}"
                    aria-label="From date"
                    class="peer block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500
                           sm:[&::-webkit-datetime-edit]:text-inherit"
                    x-bind:class="{
                        '[&::-webkit-datetime-edit]:text-transparent sm:[&::-webkit-datetime-edit]:text-inherit': !v
                    }"
                />
                <span
                    x-show="!v"
                    class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-gray-400 peer-focus:hidden sm:hidden"
                >YYYY-MM-DD</span>
            </div>
        </div>

        {{-- To --}}
        <div class="min-w-0">
            <label for="to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">To</label>

            <div x-data="{ v: @js($to) }" class="relative mt-1">
                <input
                    x-model="v"
                    type="date"
                    id="to"
                    name="to"
                    value="{{ $to }}"
                    aria-label="To date"
                    class="peer block w-full rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500
                           sm:[&::-webkit-datetime-edit]:text-inherit"
                    x-bind:class="{
                        '[&::-webkit-datetime-edit]:text-transparent sm:[&::-webkit-datetime-edit]:text-inherit': !v
                    }"
                />
                <span
                    x-show="!v"
                    class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-sm text-gray-400 peer-focus:hidden sm:hidden"
                >YYYY-MM-DD</span>
            </div>
        </div>

        {{-- Per page --}}
        <div class="min-w-0">
            <label for="per_page" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Per page</label>
            <select
                id="per_page"
                name="per_page"
                aria-label="Results per page"
                class="mt-1 block w-full sm:min-w-[6.5rem] rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
                @foreach($perPageOpts as $n)
                    <option value="{{ $n }}" @selected($perPage === $n)>{{ $n }}</option>
                @endforeach
            </select>
        </div>

        {{-- Spacer to balance columns on desktop --}}
        <div class="hidden sm:block"></div>

        {{-- Actions (inline on desktop, stacked on mobile) --}}
        <div class="flex flex-col gap-2 sm:col-span-1 sm:flex-row sm:justify-end">
            <a href="{{ $action }}" class="{{ $btnSecondary }} px-3 py-2 text-sm !text-nowrap">Clear</a>
            <button type="submit" class="{{ $btnPrimary }} px-3 py-2 text-sm !text-nowrap">Apply filters</button>
        </div>
    </form>
</div>
