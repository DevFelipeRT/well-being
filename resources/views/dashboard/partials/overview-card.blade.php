{{-- resources/views/dashboard/partials/overview-card.blade.php
    Compact overview card built on <x-card>.

    Headline:
    - Normalizes "trend.label" and shows signed "trend.delta_pct" with one decimal place.

    Footer:
    - Uses percentage-based weekday distribution from the server.
    - Suppressed when there is no data (weekday_has_data = false) or no extremes.
--}}

@props([
    'title'    => '',
    'overview' => null,
])

@php
    $o = is_array($overview ?? null) ? $overview : [];

    // Headline normalization
    $trend = is_array($o['trend'] ?? null) ? $o['trend'] : null;
    $headlineLabel = '—';
    $deltaRaw = null;

    if (is_array($trend) && array_key_exists('label', $trend)) {
        $label    = (string) $trend['label'];
        $deltaRaw = $trend['delta_pct'] ?? null;

        $headlineLabel = match (trim(strtolower($label))) {
            'improving' => 'Improving',
            'declining' => 'Declining',
            'stable'    => 'Stable',
            default     => ucfirst(trim($label)),
        };
    }

    $headlineDelta = null;
    if ($deltaRaw !== null && $deltaRaw !== '') {
        $headlineDelta = sprintf('%+0.1f%%', (float) $deltaRaw);
    }

    $statusKey  = isset($trend['label']) ? trim(strtolower((string) $trend['label'])) : null;
    $deltaColor = match ($statusKey) {
        'improving' => 'text-emerald-600 dark:text-emerald-400',
        'declining' => 'text-rose-600 dark:text-rose-400',
        'stable'    => 'text-amber-600 dark:text-amber-400',
        default     => 'text-sky-600 dark:text-sky-400',
    };

    // Weekday data
    $dist     = is_array($o['weekday_distribution'] ?? null) ? $o['weekday_distribution'] : [];
    $totals   = is_array($o['weekday_totals'] ?? null) ? $o['weekday_totals'] : [];
    $ext      = is_array($o['weekday_extremes'] ?? null) ? $o['weekday_extremes'] : [];
    $hasData  = (bool) ($o['weekday_has_data'] ?? false);

    $best  = isset($ext['best'])  && is_string($ext['best'])  && $ext['best']  !== '' ? $ext['best']  : null;
    $worst = isset($ext['worst']) && is_string($ext['worst']) && $ext['worst'] !== '' ? $ext['worst'] : null;

    $bestPct  = ($best !== null  && array_key_exists($best,  $dist)) ? $dist[$best]  : null;
    $worstPct = ($worst !== null && array_key_exists($worst, $dist)) ? $dist[$worst] : null;

    $footerClass = 'flex items-center justify-center text-center gap-10';
@endphp

<x-card
    :title="$title"
    :padding="'px-6 py-4'"
    :containerClass="'flex flex-col h-full'"
    :bodyClass="'grow'"
    :footerClass="$footerClass"
>
    <div class="flex flex-col items-center gap-3">
        <div class="min-w-0 text-center">
            <div class="text-2xl sm:text-3xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                {{ $headlineLabel }}
            </div>
        </div>

        @if($headlineDelta !== null)
            <div class="min-w-0 text-center">
                <div class="text-3xl sm:text-4xl font-bold leading-none {{ $deltaColor }}">
                    {{ $headlineDelta }}
                </div>
            </div>
        @endif
    </div>

    @if($hasData && ($best !== null || $worst !== null))
        <x-slot:footer>
            <div class="flex flex-wrap items-center justify-center gap-x-8">
                @if($best !== null)
                    <span
                        class="inline-flex items-center gap-3 text-sm text-gray-800 dark:text-gray-200"
                        role="note"
                        aria-label="{{ 'Best recurring day: ' . $best . ($bestPct !== null ? ', ' . $bestPct . '%' : '') }}"
                        title="{{ 'Best recurring day: ' . $best . ($bestPct !== null ? ' • ' . $bestPct . '%' : '') }}"
                        tabindex="0"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-[20px] w-[20px] shrink-0 text-amber-500 dark:text-amber-400 icon icon-tabler icons-tabler-outline icon-tabler-crown"
                            aria-hidden="true"
                        >
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 6l4 6l5 -4l-2 10h-14l-2 -10l5 4z" />
                        </svg>

                        <div class="inline-flex items-center gap-1">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                class="h-[22px] w-[22px] shrink-0 text-emerald-600 dark:text-emerald-400"
                                aria-hidden="true"
                            >
                                <path d="M12 5l9 14H3l9-14z"/>
                            </svg>
                            <span class="whitespace-nowrap font-semibold text-emerald-700 dark:text-emerald-300">
                                {{ $best }}
                            </span>
                        </div>
                    </span>
                @endif

                @if($worst !== null)
                    <span
                        class="inline-flex items-center gap-3 text-sm text-gray-800 dark:text-gray-200"
                        role="note"
                        aria-label="{{ 'Worst recurring day: ' . $worst . ($worstPct !== null ? ', ' . $worstPct . '%' : '') }}"
                        title="{{ 'Worst recurring day: ' . $worst . ($worstPct !== null ? ' • ' . $worstPct . '%' : '') }}"
                        tabindex="0"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            class="h-[20px] w-[20px] shrink-0 text-slate-500 dark:text-slate-400 icon icon-tabler icons-tabler-outline icon-tabler-alert-triangle"
                            aria-hidden="true"
                        >
                            <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                            <path d="M12 9v4" />
                            <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h16.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
                            <path d="M12 16h.01" />
                        </svg>

                        <div class="inline-flex items-center gap-1">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 24 24"
                                fill="currentColor"
                                class="h-[22px] w-[22px] shrink-0 text-rose-600 dark:text-rose-400"
                                aria-hidden="true"
                            >
                                <path d="M12 19L3 5h18l-9 14z"/>
                            </svg>
                            <span class="whitespace-nowrap font-semibold text-rose-700 dark:text-rose-300">
                                {{ $worst }}
                            </span>
                        </div>
                    </span>
                @endif
            </div>
        </x-slot:footer>
    @endif
</x-card>
