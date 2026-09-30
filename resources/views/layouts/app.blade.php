{{-- Layout general: mismo shell que el dashboard (barra lateral, barra superior y tema). --}}
<x-dashboard-layout :title="$title ?? config('app.name', 'Help-Dasa')">
    @isset($header)
        {{ $header }}
    @endisset

    {{ $slot }}
</x-dashboard-layout>
