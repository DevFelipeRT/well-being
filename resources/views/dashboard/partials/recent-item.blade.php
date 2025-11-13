{{-- resources/views/dashboard/partials/recent-item.blade.php --}}

@php
    $dateLabel   = $dateLabel   ?? '—';
    $scoreLabel  = $scoreLabel  ?? '—';
    $notePreview = $notePreview ?? '—';
    $showHref    = $showHref    ?? '#';
    $editHref    = $editHref    ?? null;
    $canEdit     = (bool) ($canEdit ?? false);

    // HTML do detalhe (pré-renderizado no servidor). Pode vir vazio.
    $detailsHtml = $detailsHtml ?? null;
@endphp

<li x-data="{}">
    <div
        class="flex w-full items-center justify-between py-3 gap-2 sm:gap-4 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800/50"
        role="button"
        @click="
            const tpl = $refs.detailsTpl;
            if (tpl) {
                const html = tpl.innerHTML;
                window.dispatchEvent(new CustomEvent('recent-details:open', { detail: { html } }));
            } else {
                window.location.href = @js($showHref);
            }
        "
        @keydown.enter.prevent.stop="
            const tpl = $refs.detailsTpl;
            if (tpl) {
                const html = tpl.innerHTML;
                window.dispatchEvent(new CustomEvent('recent-details:open', { detail: { html } }));
            } else {
                window.location.href = @js($showHref);
            }
        "
        @keydown.space.prevent.stop="
            const tpl = $refs.detailsTpl;
            if (tpl) {
                const html = tpl.innerHTML;
                window.dispatchEvent(new CustomEvent('recent-details:open', { detail: { html } }));
            } else {
                window.location.href = @js($showHref);
            }
        "
    >
        <div class="min-w-0">
            <p class="text-sm font-bold text-gray-900 dark:text-gray-100">
                {{ $dateLabel }}
            </p>
            <p class="w-full shrink min-w-0 mt-0.5 truncate break-words text-sm text-gray-600 dark:text-gray-300">
                Score: <span class="font-semibold">{{ $scoreLabel }}</span>
                <span class="mx-2 text-gray-400">•</span>
                {{ $notePreview }}
            </p>
        </div>

        <div class="hidden lg:flex items-center gap-2 shrink-0">
            @if($canEdit && $editHref)
                <a
                    href="{{ $editHref }}"
                    @click.stop
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-700"
                >
                    Edit
                </a>
            @endif
        </div>
    </div>

    @if(!empty($detailsHtml))
        <template x-ref="detailsTpl">{!! $detailsHtml !!}</template>
    @endif
</li>
