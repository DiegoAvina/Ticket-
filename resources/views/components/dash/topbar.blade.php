@php
    $usuario = auth()->user();
    $rol = $usuario->getRoleNames()->first() ?? 'usuario';
@endphp

<header class="sticky top-0 z-30 h-16 flex items-center justify-between gap-4 px-4 sm:px-6 bg-surface/85 backdrop-blur-xl border-b border-outline-variant/20">
    <div class="flex items-center gap-3 flex-1 max-w-2xl">
        <button type="button" class="lg:hidden p-2 -ml-2 rounded-lg text-on-surface-variant hover:bg-surface-container-high" @click="menu = true" aria-label="Abrir menú">
            <span class="material-symbols-outlined">menu</span>
        </button>

        {{-- En el dashboard filtra en vivo (evento dash-search); con Enter busca en el listado de tickets. --}}
        <form method="GET" action="{{ route('tickets.index') }}" role="search" class="relative w-full hidden sm:flex items-center"
              x-data @keydown.window.ctrl.k.prevent="$refs.buscar.focus()" @keydown.window.meta.k.prevent="$refs.buscar.focus()">
            <label for="buscar-global" class="sr-only">Buscar tickets</label>
            <span class="material-symbols-outlined absolute left-3 text-outline pointer-events-none text-[20px]">search</span>
            <input id="buscar-global" x-ref="buscar" type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por folio, título o solicitante…"
                   @input="$dispatch('dash-search', $event.target.value)"
                   class="w-full pl-10 pr-14 py-2 text-xs rounded-lg bg-surface-container-lowest text-on-surface placeholder:text-outline border border-outline-variant/40 focus:outline-none focus:ring-1 focus:ring-tertiary focus:border-tertiary transition">
            <kbd class="absolute right-2 font-code text-[11px] bg-surface-container text-on-surface-variant px-1.5 py-0.5 rounded">Ctrl K</kbd>
        </form>
    </div>

    <div class="flex items-center gap-2 sm:gap-3">
        <a href="{{ route('tickets.create') }}"
           class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary-container text-on-primary-container font-medium shadow-[0_2px_12px_rgb(var(--primary)/0.25)] hover:brightness-105 transition">
            <span class="material-symbols-outlined text-[20px]">add_circle</span>
            Nuevo ticket
        </a>

        <x-dash.theme-toggle />

        <div class="flex items-center gap-2 pl-1">
            <div class="relative">
                <x-dash.avatar :name="$usuario->name" class="w-8 h-8" />
                <span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-tertiary ring-2 ring-surface"></span>
            </div>
            <div class="hidden md:flex flex-col leading-tight">
                <span class="font-display font-semibold text-on-surface">{{ $usuario->name }}</span>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-outline">{{ $rol }}</span>
            </div>
        </div>
    </div>
</header>
