{{-- Shell de la aplicación: barra lateral + barra superior, con modo claro/oscuro. --}}
@props(['title' => 'Dashboard'])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>{{ $title }} · {{ config('app.name', 'Help-Dasa') }}</title>
        @include('layouts.partials.head')
    </head>
    <body class="font-inter text-sm antialiased bg-surface text-on-surface transition-colors duration-300"
          x-data="{ menu: false }" @keydown.escape.window="menu = false">

        <x-dash.sidebar />

        {{-- Fondo del menú en móvil --}}
        <div x-show="menu" x-transition.opacity x-cloak @click="menu = false"
             class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm lg:hidden"></div>

        <div class="lg:pl-64">
            <x-dash.topbar />

            <main class="px-4 sm:px-6 py-6">
                <div class="flex flex-col gap-6 max-w-[1600px] mx-auto">
                    @if (session('success'))
                        <x-dash.alert type="success" dismissible>{{ session('success') }}</x-dash.alert>
                    @endif
                    @if (session('error'))
                        <x-dash.alert type="error" dismissible>{{ session('error') }}</x-dash.alert>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>

        {{ $scripts ?? '' }}
    </body>
</html>
