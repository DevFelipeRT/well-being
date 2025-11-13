{{-- resources/views/check-ins/edit.blade.php --}}

@php
    $btnPrimary   = 'inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500';
    $btnSecondary = 'inline-flex items-center rounded-md border border-gray-300 dark:border-gray-600 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:text-gray-200 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-400';
    $btnDanger    = 'inline-flex items-center rounded-md bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500';

    // Compute a safe "back" URL with fallback to the index
    $previous  = url()->previous();
    $current   = url()->current();
    $prevHost  = parse_url($previous ?? '', PHP_URL_HOST);
    $safeHost  = request()->getHost();

    $backUrl = $previous
        && $previous !== $current
        && $prevHost === $safeHost
        ? $previous
        : route('check-ins.index');
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            Edit Check-in
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8 space-y-6">

            {{-- Card: form --}}
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow ring-1 ring-gray-200 dark:ring-gray-700">
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Update entry</h3>
                </div>

                <div class="p-6">
                    @include('check-ins.partials.form', [
                        'action'       => route('check-ins.update', $checkIn),
                        'method'       => 'PUT',
                        'submitLabel'  => 'Update',
                        'checkIn'      => $checkIn,
                        'defaultDate'  => null,
                        // New: cancel should return to the previous page
                        'cancelHref'   => $backUrl,
                        'cancelLabel'  => 'Cancel',
                    ])
                </div>
            </div>

            {{-- Card: danger zone --}}
            @can('delete', $checkIn)
                <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 shadow ring-1 ring-gray-200 dark:ring-gray-700">
                    <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Danger zone</h3>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">Deleting is permanent and cannot be undone.</p>
                    </div>

                    <div class="p-6 flex items-center justify-end">
                        <form action="{{ route('check-ins.destroy', $checkIn) }}" method="POST" onsubmit="return confirm('Delete this check-in?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="{{ $btnDanger }}">Delete</button>
                        </form>
                    </div>
                </div>
            @endcan

            {{-- Bottom actions (Back uses safe previous URL) --}}
            <div class="flex items-center justify-between">
                <a href="{{ $backUrl }}" class="{{ $btnSecondary }}">Back</a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('check-ins.show', $checkIn) }}" class="{{ $btnSecondary }}">View</a>
                    <a href="{{ route('check-ins.create') }}" class="{{ $btnPrimary }}">New check-in</a>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
