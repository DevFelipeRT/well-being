{{-- resources/views/dashboard/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto space-y-6">

            @php
                $hasToday   = isset($todayCheckIn) && $todayCheckIn;
                $viewUrl    = $hasToday ? route('check-ins.show', $todayCheckIn) : null;
                $editUrl    = $hasToday ? route('check-ins.edit', $todayCheckIn) : null;
                $createUrl  = !$hasToday ? route('check-ins.create') : null;
            @endphp

            @include('dashboard.partials.notice-card', [
                'todayCheckIn' => $todayCheckIn ?? null,
                'viewUrl'      => $viewUrl,
                'editUrl'      => $editUrl,
                'createUrl'    => $createUrl,
            ])

            <div>
                <h3 class="w-full px-1 py-2 mb-6 border-b border-gray-800/40 dark:border-white/40 ">
                    <span class="font-semibold text-xl text-gray-800 dark:text-gray-200">Well-being score</span>
                </h3>
                {{-- Summary grid now encapsulates all summary logic --}}
                @include('dashboard.partials.summary-grid', [
                    'overview'     => $overall ?? null,
                    'summary7'     => $summary7 ?? null,
                    'summary30'    => $summary30 ?? null,
                    'summaryMonth' => $summaryMonth ?? null,
                ])
            </div>

            {{-- Recent activity --}}
            @php
                $items = [];
                if (isset($recentItems) && $recentItems instanceof \Illuminate\Support\Collection && $recentItems->count() > 0) {
                    foreach ($recentItems as $item) {
                        $items[] = [
                            'dateLabel'   => optional($item->checked_at)->toDateString() ?? '—',
                            'scoreLabel'  => $item->score,
                            'notePreview' => \Illuminate\Support\Str::limit($item->note ?? '—', 80),
                            'showHref'    => route('check-ins.show', $item),
                            'editHref'    => route('check-ins.edit', $item),
                            'canEdit'     => auth()->check() ? auth()->user()->can('update', $item) : false,
                            'detailsHtml' => view('check-ins.partials.details-card', ['checkIn' => $item])->render(),
                        ];
                    }
                }
            @endphp

            <div>
                <div class="inline-flex w-full items-baseline justify-between mb-6 border-b border-gray-800/40 dark:border-white/40">
                    <h3 class="w-full px-1 py-2">
                        <span class="font-semibold text-xl text-gray-800 dark:text-gray-200">Recent activity</span>
                    </h3>
                    <a
                        href="{{ route('check-ins.index') }}"
                        class="px-1 py-2 text-sm text-nowrap font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300"
                    >
                        See all
                    </a>
                </div>

                @include('dashboard.partials.recent-list', [
                    'title'            => 'Recent activity',
                    'seeAllHref'       => route('check-ins.index'),
                    'items'            => $items,
                    'emptyMessage'     => 'No recent activity found. Create a new entry to get started.',
                    'initialModalHtml' => session('recent_details_html'),
                ])
            </div>

            @if (session()->has('demo_user_id'))
                <div>
                    <h3 class="w-full px-1 py-2 mb-6 border-b border-gray-800/40 dark:border-white/40 ">
                        <span class="font-semibold text-xl text-gray-800 dark:text-gray-200">Demonstration controls</span>
                    </h3>
                    @include('components.demo-controls')
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
