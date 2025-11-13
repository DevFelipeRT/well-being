{{-- resources/views/check-ins/partials/summary-row.blade.php

Top summary row with two metrics.
--}}

@php
    $summary7  = is_array($summary7 ?? null)  ? $summary7  : [];
    $summary30 = is_array($summary30 ?? null) ? $summary30 : [];
    $summaryMonth = is_array($summaryMonth ?? null) ? $summaryMonth : [];

    $card7 = [
        'title'     => 'Last 7 days',
        'summary'   => $summary7
    ];

    $card30 = [
        'title'     => 'Last 30 days',
        'summary'   => $summary30
    ];
    
    $cardMonth = [
        'title'     => 'This month',
        'summary'   => $summaryMonth
    ];
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    @include('dashboard.partials.summary-card', $card7)
    @include('dashboard.partials.summary-card', $card30)
    @include('dashboard.partials.summary-card', $cardMonth)
</div>
