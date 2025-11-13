{{-- resources/views/components/action-menu.blade.php

Mobile action menu with customizable trigger, backdrop, placement, and item styling.
Accessible (ARIA, Escape-to-close, focus on first item). Supports link and form items.
Automatically flips up when there is not enough space below the trigger.

Props:
- array<int, array<string,mixed>> $items
    Each item:
      type: 'link'|'form' (default 'link')
      label: string
      href?: string
      action?: string
      method?: 'POST'|'PUT'|'PATCH'|'DELETE' (default 'POST')
      confirm?: string
      classes?: string
- string|null  $triggerAriaLabel   Accessible label for the trigger button.
- string|null  $triggerClass       Tailwind classes for the trigger button.
- string|null  $triggerContent     Raw HTML for a custom trigger (icon, label, etc.).
- bool|null    $backdrop           Whether to show a blocking backdrop (default true).
- string|null  $backdropClass      Backdrop classes (default 'bg-black/30').
- string|null  $panelClass         Container classes for the menu panel.
- string|null  $itemClass          Base classes for each item.
- string|null  $width              Panel width utility (e.g., 'w-48').
- string|null  $placement          'right'|'left' (default 'right').
- string|null  $offsetClass        Extra vertical offset utility (optional).
- string|null  $zIndex             Z-index utility (default 'z-50').
- string|null  $showOn             Visibility utility for wrapper (default 'sm:hidden').
- bool|null    $closeOnSelect      Close when an item is activated (default true).
- int|float|null $flipGuard        Minimum space (px) to keep opening down (default 0).
--}}

@props([
    'items'            => [],
    'triggerAriaLabel' => 'Open actions',
    'triggerClass'     => 'inline-flex items-center justify-center p-2 rounded-md text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100 dark:hover:bg-gray-700 focus:outline-none focus-within:bg-gray-700',
    'triggerContent'   => null,
    'backdrop'         => true,
    'backdropClass'    => 'bg-black/30',
    'panelClass'       => 'rounded-md bg-white dark:bg-gray-900 shadow-lg ring-1 ring-black/5 p-2',
    'itemClass'        => 'w-full text-left text-sm px-3 py-2 rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500',
    'width'            => 'w-44',
    'placement'        => 'right',
    'offsetClass'      => null,
    'zIndex'           => 'z-50',
    'showOn'           => 'sm:hidden',
    'closeOnSelect'    => true,
    'flipGuard'        => 0,
])

@php
    $isLeft      = ($placement === 'left');

    $sideClass   = $isLeft ? 'left-0' : 'right-0';

    $originTop   = $isLeft ? 'origin-top-left'    : 'origin-top-right';
    $originBottom= $isLeft ? 'origin-bottom-left' : 'origin-bottom-right';

    $extraOffset = $offsetClass ? trim($offsetClass) : '';

    $wrapperCls  = trim("relative {$showOn}");
    $menuId      = uniqid('amenu_', true);
    $btnId       = uniqid('amenu_btn_', true);

    $flipGuardNumeric = is_numeric($flipGuard) ? (float) $flipGuard : 0.0;
@endphp

<div
    x-data="{
        open: false,
        openUp: false,
        flipGuard: {{ $flipGuardNumeric }},

        close() {
            this.open = false;
        },

        toggle() {
            if (this.open) {
                this.close();
                return;
            }

            this.open = true;

            this.$nextTick(() => {
                this.recalculateDirection();
                this.focusFirst();
            });
        },

        recalculateDirection() {
            const trigger = this.$refs.trigger;
            const panel   = this.$refs.panel;

            if (!trigger || !panel) {
                return;
            }

            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            const triggerRect    = trigger.getBoundingClientRect();
            const panelRect      = panel.getBoundingClientRect();

            const panelHeight    = panelRect.height || 0;
            const spaceBelow     = viewportHeight - triggerRect.bottom;
            const spaceAbove     = triggerRect.top;
            const guard          = Number(this.flipGuard) || 0;

            const flipByGuard  = spaceBelow <= guard;
            const flipByHeight = spaceBelow < panelHeight;

            // Flip up if:
            // - guard says there is not enough safe space below; or
            // - actual panel height does not fit below;
            // and there is more room above than below.
            this.openUp = (flipByGuard || flipByHeight) && spaceAbove > spaceBelow;
        },

        focusFirst() {
            const first = this.$refs.firstItem;
            if (first && typeof first.focus === 'function') {
                first.focus();
            }
        },

        panelPositionClasses() {
            const extra = '{{ $extraOffset }}';

            if (this.openUp) {
                return '{{ $originBottom }} bottom-full mb-2 ' + extra;
            }

            return '{{ $originTop }} top-full mt-2 ' + extra;
        }
    }"
    class="{{ $wrapperCls }}"
    @keydown.escape.window="close()"
    @click.stop
>
    <button
        id="{{ $btnId }}"
        x-ref="trigger"
        type="button"
        x-on:click="toggle()"
        x-bind:aria-expanded="open"
        aria-haspopup="menu"
        aria-controls="{{ $menuId }}"
        aria-label="{{ $triggerAriaLabel }}"
        class="{{ $triggerClass }}"
    >
        @if($triggerContent)
            {!! $triggerContent !!}
        @else
            {{-- Tabler Dots Vertical (inline SVG) --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 5v.01"/><path d="M12 12v.01"/><path d="M12 19v.01"/>
            </svg>
        @endif
    </button>

    @if($backdrop)
        <div
            x-cloak
            x-show="open"
            x-transition.opacity
            class="fixed inset-0 {{ $zIndex }} {{ $backdropClass }}"
            aria-hidden="true"
            @click="close()"
        ></div>
    @endif

    <div
        id="{{ $menuId }}"
        x-cloak
        x-show="open"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        x-ref="panel"
        :class="panelPositionClasses()"
        class="absolute {{ $sideClass }} {{ $zIndex }} {{ $width }} {{ $panelClass }}"
        role="menu"
        aria-labelledby="{{ $btnId }}"
        @click.outside="{{ $backdrop ? '' : 'close()' }}"
    >
        <div class="flex flex-col gap-1" role="none">
            @foreach($items as $index => $i)
                @php
                    $type       = ($i['type'] ?? 'link');
                    $label      = (string) ($i['label'] ?? 'Action');
                    $classes    = trim(($i['classes'] ?? '') . ' ' . $itemClass);
                    $confirmMsg = $i['confirm'] ?? null;
                    $method     = strtoupper($i['method'] ?? 'POST');
                    $href       = $i['href'] ?? '#';
                    $action     = $i['action'] ?? '#';
                    $firstAttr  = $index === 0 ? 'x-ref=firstItem' : '';
                @endphp

                @if($type === 'form')
                    <form
                        action="{{ $action }}"
                        method="POST"
                        role="none"
                        onsubmit="return {{ $confirmMsg !== null ? 'confirm('.json_encode($confirmMsg).')' : 'true' }};"
                        @submit.stop
                    >
                        @csrf
                        @if($method !== 'POST')
                            @method($method)
                        @endif
                        <button
                            type="submit"
                            {!! $firstAttr !!}
                            role="menuitem"
                            tabindex="-1"
                            class="{{ $classes }}"
                            @click="{{ $closeOnSelect ? 'close()' : '' }}"
                        >{{ $label }}</button>
                    </form>
                @else
                    <a
                        href="{{ $href }}"
                        {!! $firstAttr !!}
                        role="menuitem"
                        tabindex="-1"
                        class="{{ $classes }}"
                        @click="{{ $closeOnSelect ? 'close()' : '' }}"
                    >{{ $label }}</a>
                @endif
            @endforeach
        </div>
    </div>
</div>
