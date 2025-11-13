{{-- resources/views/dashboard/partials/summary-grid.blade.php
    Responsive grid for overview + period summaries.

    The Overview tile delegates the full "overview" payload to <x-overview-card>
    (trend + weekday data). Each period summary delegates a single "summary"
    payload to <x-summary-card>, removing any dependency on average/count/metaLines.
    The summary-card is responsible for rendering the main metric, count, and a
    footer visually consistent with the overview-card (Best/Worst weekday icons).

    Props:
    - array|null $overview
      Keys:
        • trend:               { label:string, delta_pct:float|int|null }
        • weekday_extremes:    { best?:string|null, worst?:string|null }  // "Mon"…"Sun"
        • weekday_distribution: array<string,int>                          // optional

    - array|null $summary7
    - array|null $summary30
    - array|null $summaryMonth
      Expected shape for each summary payload (passed through verbatim):
        • average_score: float|null
        • count:         int
        • min_score:     int
        • max_score:     int
        • first_date:    string(YYYY-MM-DD)
        • last_date:     string(YYYY-MM-DD)
        • best_day:      { date:string(YYYY-MM-DD), score:int }
        • worst_day:     { date:string(YYYY-MM-DD), score:int }
        • weekday_distribution?: array<string,int>                          // optional
        • extremes_override?:   { best?:string|null, worst?:string|null }   // optional

    Notes:
    - No formatting or derivations here. The grid simply forwards payloads.
--}}

@props([
    'overview'     => null,
    'summary7'     => null,
    'summary30'    => null,
    'summaryMonth' => null,
])

@php
    // Assemble the three period cards with title + raw summary payload.
    // Summaries are passed verbatim; the summary-card handles guards/rendering.
    $periodCards = [
        ['title' => 'Last 7 days',  'summary' => is_array($summary7)     ? $summary7     : []],
        ['title' => 'Last 30 days', 'summary' => is_array($summary30)    ? $summary30    : []],
        ['title' => 'This month',   'summary' => is_array($summaryMonth) ? $summaryMonth : []],
    ];
@endphp

<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
    {{-- Overview --}}
    @if(is_array($overview))
        @include('dashboard.partials.overview-card', [
            'title'    => 'Overview',
            'trend'    => $overview['trend'] ?? null,
            'overview' => $overview,
        ])
    @endif

    {{-- Period summaries (each passes title + summary payload directly) --}}
    @foreach($periodCards as $c)
        @include('dashboard.partials.summary-card', [
            'title'   => $c['title'],
            'summary' => $c['summary'],
        ])
    @endforeach
</div>
