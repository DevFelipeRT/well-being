{{-- resources/views/dashboard/partials/recent-list.blade.php --}}

@php
    $title             = $title             ?? 'Recent activity';
    $seeAllHref        = $seeAllHref        ?? '#';
    $items             = (isset($items) && is_array($items)) ? $items : [];
    $emptyMessage      = $emptyMessage      ?? 'No recent activity found. Create a new entry to get started.';
    $initialModalHtml  = $initialModalHtml  ?? null;
@endphp

<div
    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-md"
    x-data="{ modalHtml: '' }"
    x-on:recent-details:open.window="
        modalHtml = $event.detail?.html || '';
        if (modalHtml) {
            window.dispatchEvent(new CustomEvent('open-modal', { detail: 'recent-details' }));
        }
    "
    x-init="
    @if(!empty($initialModalHtml))
        modalHtml = @js($initialModalHtml);

        $nextTick(() => {
            requestAnimationFrame(() => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'recent-details' }));
            });
        });
    @endif
"

>
    <div class="p-6">
        @if(!empty($items))
            <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($items as $item)
                    @include('dashboard.partials.recent-item', [
                        'dateLabel'   => $item['dateLabel']   ?? '—',
                        'scoreLabel'  => $item['scoreLabel']  ?? '—',
                        'notePreview' => $item['notePreview'] ?? '—',
                        'showHref'    => $item['showHref']    ?? '#',
                        'editHref'    => $item['editHref']    ?? null,
                        'canEdit'     => (bool) ($item['canEdit'] ?? false),
                        'detailsHtml' => $item['detailsHtml'] ?? null,
                    ])
                @endforeach
            </ul>
        @else
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                {{ $emptyMessage }}
            </p>
        @endif
    </div>

    <x-modal name="recent-details" :show="false" maxWidth="2xl" position="center">
        <div class="p-4 sm:p-6" x-html="modalHtml"></div>
    </x-modal>
</div>
