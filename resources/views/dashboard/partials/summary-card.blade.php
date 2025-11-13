{{-- resources/views/dashboard/partials/summary-card.blade.php
    Summary card built on <x-card>.

    Renders a period summary from a single "summary" payload:
    • Headline shows the period average; subtitle shows the total entries.
    • Footer highlights the concrete best and worst calendar dates within the period,
      using filled directional triangles and compact day/month labels without year.
      Tooltips expose the ISO date and the score.

    Props:
    - string                   $title
      Forwarded to <x-card>.

    - array<string,mixed>      $summary
      Keys:
        • average_score: float|null
        • count:         int
        • best_day?:     { date:string(YYYY-MM-DD), score:int }
        • worst_day?:    { date:string(YYYY-MM-DD), score:int }
--}}

@props([
    'title'   => '',
    'summary' => [],
])

@php
    $s = is_array($summary ?? null) ? $summary : [];

    // Headline metric and count
    $avg = array_key_exists('average_score', $s) && $s['average_score'] !== null && $s['average_score'] !== ''
        ? (string) round((float) $s['average_score'], 2)
        : '—';

    $cnt = (int) ($s['count'] ?? 0);

    // Helpers to format dates without year and to guard payloads
    $fmtDayNoYear = static function (?string $iso): ?string {
        if ($iso === null || trim($iso) === '') {
            return null;
        }
        try {
            return \Carbon\Carbon::parse($iso)->format('d/m'); // DD/MM
        } catch (\Throwable $e) {
            return null;
        }
    };

    $best = null; // ['date_ddmm' => '12/11', 'date_iso' => '2025-11-12', 'score' => 5]
    if (isset($s['best_day']) && is_array($s['best_day'])) {
        $d   = (string) ($s['best_day']['date']  ?? '');
        $sc  = isset($s['best_day']['score']) ? (int) $s['best_day']['score'] : null;
        $ddm = $fmtDayNoYear($d);
        if ($ddm !== null && $sc !== null) {
            $best = ['date_ddmm' => $ddm, 'date_iso' => $d, 'score' => $sc];
        }
    }

    $worst = null; // ['date_ddmm' => '11/11', 'date_iso' => '2025-11-11', 'score' => 3]
    if (isset($s['worst_day']) && is_array($s['worst_day'])) {
        $d   = (string) ($s['worst_day']['date'] ?? '');
        $sc  = isset($s['worst_day']['score']) ? (int) $s['worst_day']['score'] : null;
        $ddm = $fmtDayNoYear($d);
        if ($ddm !== null && $sc !== null) {
            $worst = ['date_ddmm' => $ddm, 'date_iso' => $d, 'score' => $sc];
        }
    }

    // Accessible titles
    $bestTitle  = $best  ? ('Best day: '  . $best['date_iso']  . ' • score ' . $best['score'])  : null;
    $worstTitle = $worst ? ('Worst day: ' . $worst['date_iso'] . ' • score ' . $worst['score']) : null;

    // Footer spacing aligned with overview-card
    $footerClass = 'flex items-center justify-center text-center gap-10';
@endphp

<x-card
    :title="$title"
    :padding="'px-6 py-4'"
    :containerClass="'flex flex-col h-full'"
    :bodyClass="'grow content-center'"
    :footerClass="$footerClass"
>
    {{-- Body --}}
    <div class="flex flex-col items-center gap-4">
        <div class="min-w-0 text-center">
            <div class="text-3xl sm:text-4xl font-semibold leading-tight text-gray-900 dark:text-gray-100">
                {{ $avg }}
            </div>
            <div class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                {{ $cnt }} {{ \Illuminate\Support\Str::plural('entry', $cnt) }}
            </div>
        </div>
    </div>

    {{-- Footer: concrete best/worst dates (no year) --}}
    @if($best !== null || $worst !== null)
        <x-slot:footer>
            <div class="flex flex-wrap w-full items-center justify-center gap-x-8">
                @if($best !== null)
                    <span
                        class="inline-flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200"
                        role="note"
                        aria-label="{{ 'Best day: ' . $best['date_iso'] . ', score ' . $best['score'] }}"
                        title="{{ $bestTitle }}"
                        tabindex="0"
                    >
                        {{-- Up filled triangle --}}
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
                            {{ $best['date_ddmm'] }} • {{ $best['score'] }}
                        </span>
                    </span>
                @endif

                @if($worst !== null)
                    <span
                        class="inline-flex items-center gap-2 text-sm text-gray-800 dark:text-gray-200"
                        role="note"
                        aria-label="{{ 'Worst day: ' . $worst['date_iso'] . ', score ' . $worst['score'] }}"
                        title="{{ $worstTitle }}"
                        tabindex="0"
                    >
                        {{-- Down filled triangle --}}
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
                            {{ $worst['date_ddmm'] }} • {{ $worst['score'] }}
                        </span>
                    </span>
                @endif
            </div>
        </x-slot:footer>
    @endif
</x-card>
