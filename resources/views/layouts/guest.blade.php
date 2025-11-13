<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Theme -->
        <meta name="theme-color" media="(prefers-color-scheme: light)" content="#ffffff">
        <meta name="theme-color" media="(prefers-color-scheme: dark)"  content="#0b0b0b">

        <title>{{ config('app.name', '') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
        <link rel="icon" href="{{ asset('favicon.ico') }}">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased bg-gray-100 dark:bg-gray-900">
        <x-toast-container />

        <div class="w-screen min-h-screen h-full flex flex-col sm:justify-center items-center pt-6 sm:pt-0 gap-6">
            <div class="pt-4">
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="flex flex-col w-full items-center sm:max-w-md overflow-hidden gap-2">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
