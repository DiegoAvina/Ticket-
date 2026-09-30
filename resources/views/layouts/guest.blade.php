<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>{{ config('app.name', 'Help-Dasa') }}</title>
        @include('layouts.partials.head')
    </head>
    <body class="font-inter text-sm antialiased bg-surface text-on-surface transition-colors duration-300">
        <div class="relative min-h-screen flex flex-col justify-center items-center px-4 py-10 overflow-hidden">
            <div class="absolute -right-32 -top-32 w-[28rem] h-[28rem] rounded-full bg-gradient-to-br from-primary-container/60 to-tertiary/20 blur-3xl pointer-events-none"></div>
            <div class="absolute -left-32 -bottom-32 w-[24rem] h-[24rem] rounded-full bg-gradient-to-tr from-secondary-container/50 to-transparent blur-3xl pointer-events-none"></div>

            <div class="absolute top-4 right-4">
                <x-dash.theme-toggle />
            </div>

            <a href="/" class="relative flex items-center gap-2">
                <x-application-logo class="w-16 h-16" />
                <span class="font-display text-2xl font-bold tracking-tight">{{ config('app.name', 'Help-Dasa') }}</span>
            </a>

            <div class="relative w-full sm:max-w-md mt-6 p-6 sm:p-8 dash-card shadow-xl">
                {{ $slot }}
            </div>

            <p class="relative mt-6 text-xs text-outline">© {{ now()->year }} {{ config('app.name', 'Help-Dasa') }}</p>
        </div>
    </body>
</html>
