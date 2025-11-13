{{-- resources/views/check-ins/partials/table.blade.php

Responsive check-ins list using Tailwind Grid/Flex only (no table tags).
- Columns (all breakpoints): Date | Score | Notes | Actions.
- Header visible on all breakpoints; Actions column without title.
- Mobile: actions via compact dropdown; ≥ sm: inline actions.

Inputs:
- \Illuminate\Contracts\Pagination\LengthAwarePaginator $items
- string $btnPrimary
- string $btnSecondary (unused)
- string $btnDanger
--}}

@php /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $items */ @endphp
<style>[x-cloak]{display:none!important}</style>

<div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow">
    @if($items->count() === 0)
        <div class="p-8 text-center text-gray-500 dark:text-gray-400">
            No check-ins found for the selected period.
        </div>
    @else
        {{-- Header --}}
        <div class="grid items-center gap-x-3 px-4 py-3
                    bg-gray-50 dark:bg-gray-900/40 border-b border-gray-200 dark:border-gray-700
                    [grid-template-columns:9ch_6ch_minmax(0,1fr)_auto]">
            <div class="text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 text-center">Date</div>
            <div class="text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 text-center">Score</div>
            <div class="text-[11px] sm:text-xs font-semibold uppercase tracking-wider text-gray-700 dark:text-gray-300 text-left">Notes</div>
            <div aria-hidden="true"></div>
        </div>

        {{-- Body --}}
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($items as $item)
                @include('check-ins.partials.table-row', [
                    'item'         => $item,
                    'btnPrimary'   => $btnPrimary,
                    'btnSecondary' => $btnSecondary ?? '',
                    'btnDanger'    => $btnDanger,
                ])
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(method_exists($items, 'links'))
            <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/40">
                {{ $items->onEachSide(1)->links() }}
            </div>
        @endif
    @endif
</div>
