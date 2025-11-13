<?php
// resources/views/check-ins/create.blade.php
?>

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">New Check-in</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-lg bg-white dark:bg-gray-800 p-6 shadow">
                @include('check-ins.partials.form', [
                    'action'      => route('check-ins.store'),
                    'method'      => 'POST',
                    'submitLabel' => 'Create',
                    'checkIn'     => null,
                    'defaultDate' => $defaultDate ?? now()->toDateString(),
                ])
            </div>
        </div>
    </div>
</x-app-layout>
