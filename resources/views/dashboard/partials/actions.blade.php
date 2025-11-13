{{-- resources/views/dashboard/partials/actions.blade.php

Action buttons group for dashboard cards.

Inputs:
- string        $primaryHref      Required primary action URL.
- string        $primaryLabel     Required primary action label.
- string|null   $primaryVariant   "solid" | "outline" | "ghost" (default: "solid").
- string|null   $secondaryHref    Optional secondary action URL.
- string|null   $secondaryLabel   Optional secondary action label.
- string|null   $secondaryVariant "solid" | "outline" | "ghost" (default: "outline").
- string|null   $justify          "start" | "center" | "end" | "between" (default: "end").

Notes:
- Presentational-only; no domain or authorization logic.
- Self-contained styles; no global side effects.
--}}

@php
    $primaryHref      = $primaryHref      ?? '#';
    $primaryLabel     = $primaryLabel     ?? 'Action';
    $primaryVariant   = $primaryVariant   ?? 'solid';

    $secondaryHref    = $secondaryHref    ?? null;
    $secondaryLabel   = $secondaryLabel   ?? null;
    $secondaryVariant = $secondaryVariant ?? 'outline';

    $justify = match ($justify ?? 'end') {
        'start'   => 'justify-start',
        'center'  => 'justify-center',
        'between' => 'justify-between',
        default   => 'justify-end',
    };

    $baseBtn = 'inline-flex items-center rounded-md px-4 py-2 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2';

    $variants = [
        'solid'   => 'bg-indigo-600 text-white hover:bg-indigo-700 focus:ring-indigo-500 dark:bg-indigo-600 dark:hover:bg-indigo-700',
        'outline' => 'border border-gray-300 text-gray-700 hover:bg-gray-50 focus:ring-gray-400 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700',
        'ghost'   => 'text-indigo-600 hover:underline focus:ring-indigo-500 dark:text-indigo-400',
    ];

    $primaryClasses   = $baseBtn . ' ' . ($variants[$primaryVariant] ?? $variants['solid']);
    $secondaryClasses = $baseBtn . ' ' . ($variants[$secondaryVariant] ?? $variants['outline']);
@endphp

<div class="flex items-center gap-2 {{ $justify }}">
    <a href="{{ $primaryHref }}" class="{{ $primaryClasses }}">
        {{ $primaryLabel }}
    </a>

    @if($secondaryHref && $secondaryLabel)
        <a href="{{ $secondaryHref }}" class="{{ $secondaryClasses }}">
            {{ $secondaryLabel }}
        </a>
    @endif
</div>
