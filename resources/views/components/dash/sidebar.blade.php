@php
    $esAdmin = auth()->user()->hasRole('administrador');

    $principal = [
        ['label' => 'Panel principal', 'icon' => 'space_dashboard', 'href' => route('dashboard'), 'active' => request()->routeIs('dashboard')],
        ['label' => 'Tickets', 'icon' => 'confirmation_number', 'href' => route('tickets.index'), 'active' => request()->routeIs('tickets.index', 'tickets.show')],
        ['label' => 'Nueva solicitud', 'icon' => 'add_circle', 'href' => route('tickets.create'), 'active' => request()->routeIs('tickets.create')],
    ];

    $administracion = $esAdmin ? [
        ['label' => 'Usuarios', 'icon' => 'group', 'href' => route('admin.users.index'), 'active' => request()->routeIs('admin.users.*')],
        ['label' => 'Departamentos', 'icon' => 'corporate_fare', 'href' => route('admin.departments.index'), 'active' => request()->routeIs('admin.departments.*')],
        ['label' => 'Servicios', 'icon' => 'design_services', 'href' => route('admin.services.index'), 'active' => request()->routeIs('admin.services.*')],
        ['label' => 'Categorías', 'icon' => 'category', 'href' => route('admin.categories.index'), 'active' => request()->routeIs('admin.categories.*')],
        ['label' => 'Prioridades', 'icon' => 'flag', 'href' => route('admin.priorities.index'), 'active' => request()->routeIs('admin.priorities.*')],
    ] : [];
@endphp

<aside class="fixed inset-y-0 left-0 z-50 w-64 flex flex-col justify-between py-6 bg-surface-container-low border-r border-outline-variant/30 transition-transform duration-300 -translate-x-full lg:translate-x-0"
       :class="menu ? 'translate-x-0' : '-translate-x-full'">
    <div class="flex flex-col min-h-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-6 pb-6">
            <x-application-logo class="h-10 w-10" />
            <span class="font-display text-xl font-bold tracking-tight">{{ config('app.name', 'Help-Dasa') }}</span>
        </a>

        <nav class="flex flex-col gap-1 px-3 overflow-y-auto no-scrollbar">
            <span class="px-3 pb-1 text-[11px] font-semibold uppercase tracking-wider text-outline">Espacio de trabajo</span>
            @foreach ($principal as $item)
                <x-dash.nav-item :item="$item" />
            @endforeach

            @if ($administracion)
                <span class="px-3 pt-5 pb-1 text-[11px] font-semibold uppercase tracking-wider text-outline">Administración</span>
                @foreach ($administracion as $item)
                    <x-dash.nav-item :item="$item" />
                @endforeach
            @endif
        </nav>
    </div>

    <div class="px-3 flex flex-col gap-2">
        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-3 py-2 rounded-lg text-on-surface-variant hover:bg-surface-container hover:text-on-surface transition-colors">
            <span class="material-symbols-outlined text-[20px]">manage_accounts</span>
            <span>Mi perfil</span>
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2 rounded-lg text-on-surface-variant hover:bg-error-container/40 hover:text-error transition-colors">
                <span class="material-symbols-outlined text-[20px]">logout</span>
                <span>Cerrar sesión</span>
            </button>
        </form>
    </div>
</aside>
