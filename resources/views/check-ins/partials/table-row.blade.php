{{-- resources/views/check-ins/partials/table-row.blade.php

Single check-in row using Tailwind Grid/Flex (no table tags).
- Full-row navigation; inner controls stop propagation.
- Columns (all breakpoints): Date (center) | Score (right, tabular) | Notes (clamped) | Actions.
- Mobile: vertical-ellipsis trigger, no background/border; ≥ sm: inline buttons.

Inputs:
- \App\Models\CheckIn $item
- string $btnPrimary
- string $btnSecondary (unused)
- string $btnDanger
--}}

@php
    /** @var \App\Models\CheckIn $item */
    $showUrl    = route('check-ins.show', $item);
    $editUrl    = route('check-ins.edit', $item);
    $canEdit    = auth()->check() ? auth()->user()->can('update', $item) : false;
    $canDelete  = auth()->check() ? auth()->user()->can('delete', $item) : false;
    $hasActions = $canEdit || $canDelete;

    $dateText   = optional($item->checked_at ?? $item->date ?? $item->created_at)->toDateString();
    $scoreText  = isset($item->score) ? (string) $item->score : '—';
    $notesText  = trim((string) ($item->notes ?? $item->note ?? ''));
@endphp

<div
    x-data="{ open:false }"
    tabindex="0"
    role="button"
    @click="window.location.href = @js($showUrl)"
    @keydown.enter.prevent.stop="window.location.href = @js($showUrl)"
    @keydown.space.prevent.stop="window.location.href = @js($showUrl)"
    class="group grid items-center gap-x-3 px-4 py-3
           cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-900/30 focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500
           [grid-template-columns:9ch_6ch_minmax(0,1fr)_auto]"
>
    {{-- Date --}}
    <div class="text-center text-sm text-gray-900 dark:text-gray-100">
        {{ $dateText ?? '—' }}
    </div>


    {{-- Score --}}
    <div class="justify-self-center text-center text-sm font-semibold tabular-nums text-gray-900 dark:text-gray-100">
        {{ $scoreText }}
    </div>

    {{-- Notes --}}
    <div class="min-w-0">
        <p class="min-w-0 text-sm text-gray-700 dark:text-gray-300 break-words hyphens-auto line-clamp-2 ">
            {{ $notesText !== '' ? $notesText : '—' }}
        </p>
    </div>

    {{-- Actions --}}
    <div class="justify-self-end relative" @click.stop>
        @if($hasActions)
            {{-- ≥ sm: inline buttons --}}
            <div class="hidden sm:flex w-[8rem] items-center gap-2">
                <div class="flex-1">
                    @if($canEdit)
                        <a href="{{ $editUrl }}" class="{{ $btnPrimary }} w-full px-2.5 py-1.5 text-xs">Edit</a>
                    @endif
                </div>
                <div class="flex-1">
                    @if($canDelete)
                        <form action="{{ route('check-ins.destroy', $item) }}" method="POST" onsubmit="return confirm('Delete this check-in?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="{{ $btnDanger }} w-full px-2.5 py-1.5 text-xs">Delete</button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Mobile: reusable action menu --}}
            <x-action-menu
                :items="array_filter([
                    $canEdit ? [
                        'type'    => 'link',
                        'href'    => $editUrl,
                        'label'   => 'Edit',
                        'classes' => $btnPrimary . ' justify-center text-xs py-1.5',
                    ] : null,
                    $canDelete ? [
                        'type'    => 'form',
                        'action'  => route('check-ins.destroy', $item),
                        'method'  => 'DELETE',
                        'label'   => 'Delete',
                        'confirm' => 'Delete this check-in?',
                        'classes' => $btnDanger . ' w-full justify-center text-xs py-1.5',
                    ] : null,
                ])"
                trigger-aria-label="Open actions"
                :flip-guard="150"
            />
        @endif
    </div>
</div>
