{{--
    Lista de tickets con pestañas de filtro y búsqueda (la barra superior
    emite `dash-search`). Filtra en el cliente sobre los tickets cargados.
    $contexto: 'solicitante' muestra el área atendiendo; 'area' muestra al solicitante.
--}}
@props([
    'tickets',
    'title' => 'Tickets recientes',
    'contexto' => 'area',
    'verTodos' => null,
])

@php
    $finalizados = ['resolved', 'closed', 'cancelled'];

    $metas = $tickets->map(function ($ticket) use ($finalizados) {
        $code = $ticket->status?->code;
        $grupos = [];
        if (! in_array($code, $finalizados, true)) $grupos[] = 'activos';
        if (($ticket->priority?->level ?? 0) >= 3 && ! in_array($code, $finalizados, true)) $grupos[] = 'alta';
        if ($code === 'waiting_user') $grupos[] = 'espera';
        if (in_array($code, ['resolved', 'closed'], true)) $grupos[] = 'resueltos';

        return [
            'grupos' => $grupos,
            'texto' => mb_strtolower(implode(' ', [$ticket->ticket_number, $ticket->title, $ticket->requester?->name, $ticket->assignedDepartment?->name])),
        ];
    })->values();

    $contar = fn ($grupo) => $metas->filter(fn ($m) => in_array($grupo, $m['grupos'], true))->count();

    $tabs = [
        ['key' => 'todos', 'label' => 'Todos', 'count' => $metas->count(), 'tone' => 'text-outline'],
        ['key' => 'activos', 'label' => 'Activos', 'count' => $contar('activos'), 'tone' => 'text-tertiary'],
        ['key' => 'alta', 'label' => 'Alta prioridad', 'count' => $contar('alta'), 'tone' => 'text-error', 'dot' => true],
        ['key' => 'espera', 'label' => 'En espera', 'count' => $contar('espera'), 'tone' => 'text-outline'],
        ['key' => 'resueltos', 'label' => 'Resueltos', 'count' => $contar('resueltos'), 'tone' => 'text-outline'],
    ];
@endphp

<div x-data="{
        tab: 'todos',
        q: '',
        rows: @js($metas),
        match(r) {
            return (this.tab === 'todos' || r.grupos.includes(this.tab))
                && (! this.q || r.texto.includes(this.q.toLowerCase()));
        },
        get visibles() { return this.rows.filter(r => this.match(r)).length },
     }"
     @dash-search.window="q = $event.detail"
     {{ $attributes->merge(['class' => 'flex flex-col gap-4 min-w-0']) }}>

    {{-- Pestañas --}}
    <div class="dash-card p-4 flex flex-col gap-2">
        <div class="flex items-center justify-between gap-2">
            <h3 class="font-display font-bold text-base text-on-surface">{{ $title }}</h3>
            @if ($verTodos)
                <a href="{{ $verTodos }}" class="inline-flex items-center gap-1 text-[11px] font-semibold uppercase tracking-wider text-tertiary hover:underline">
                    Ver todos <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                </a>
            @endif
        </div>
        <div class="flex items-center gap-1 overflow-x-auto no-scrollbar pb-1" role="tablist">
            @foreach ($tabs as $t)
                <button type="button" role="tab" @click="tab = '{{ $t['key'] }}'"
                        :aria-selected="tab === '{{ $t['key'] }}'"
                        :class="tab === '{{ $t['key'] }}'
                            ? 'bg-primary-container text-on-primary-container font-semibold shadow-sm'
                            : 'text-on-surface-variant hover:bg-surface-container hover:text-on-surface'"
                        class="flex items-center gap-1 px-4 py-1 rounded-lg whitespace-nowrap transition-colors">
                    @isset($t['dot'])
                        <span class="w-1.5 h-1.5 rounded-full bg-error"></span>
                    @endisset
                    {{ $t['label'] }}
                    <span class="ml-1 text-[11px] font-code {{ $t['tone'] }}">({{ $t['count'] }})</span>
                </button>
            @endforeach
        </div>
    </div>

    {{-- Tabla --}}
    <div class="dash-card overflow-hidden flex flex-col">
        <div class="hidden md:grid grid-cols-12 gap-2 px-4 py-2 bg-surface-container text-outline text-[11px] font-semibold uppercase tracking-wider">
            <div class="col-span-5 flex items-center gap-1">
                <span class="material-symbols-outlined text-[14px]">tag</span> Folio / Asunto
            </div>
            <div class="col-span-3">{{ $contexto === 'solicitante' ? 'Área / Servicio' : 'Solicitante / Área' }}</div>
            <div class="col-span-2">Prioridad y estado</div>
            <div class="col-span-2 text-right">SLA / Responsable</div>
        </div>

        <div class="divide-y divide-outline-variant/20">
            @foreach ($tickets->values() as $i => $ticket)
                <x-dash.ticket-row :ticket="$ticket" :contexto="$contexto" x-show="match(rows[{{ $i }}])" />
            @endforeach
        </div>

        <div x-show="visibles === 0" @if ($tickets->isNotEmpty()) x-cloak @endif class="px-4 py-12 text-center">
            <span class="material-symbols-outlined text-[40px] text-outline">inbox</span>
            <p class="mt-2 text-on-surface-variant">
                {{ $tickets->isEmpty() ? 'Todavía no hay tickets aquí.' : 'Ningún ticket coincide con el filtro.' }}
            </p>
            {{ $empty ?? '' }}
        </div>

        <div class="flex items-center justify-between px-4 py-2 bg-surface-container text-xs text-outline">
            <span>Mostrando <span x-text="visibles">{{ $tickets->count() }}</span> de {{ $tickets->count() }} tickets recientes</span>
            <span class="hidden sm:inline font-code text-tertiary">Actualizado {{ now()->format('H:i') }}</span>
        </div>
    </div>
</div>
