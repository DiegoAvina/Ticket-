<x-app-layout title="Tickets">
    <x-slot name="header">
        <x-dash.page-header title="Tickets" icon="confirmation_number"
                            :subtitle="$tickets->total().' ticket(s)'.(request('q') ? ' que coinciden con «'.request('q').'»' : '')">
            <a href="{{ route('tickets.create') }}" class="btn-primary">
                <span class="material-symbols-outlined text-[18px]">add_box</span> Nueva solicitud
            </a>
        </x-dash.page-header>
    </x-slot>

    {{-- Filtros por estado --}}
    <div class="dash-card p-4 flex flex-col gap-3">
        <form method="GET" action="{{ route('tickets.index') }}" class="flex flex-col sm:flex-row gap-2">
            @if (request('estado'))
                <input type="hidden" name="estado" value="{{ request('estado') }}">
            @endif
            <div class="relative flex-1">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px] pointer-events-none">search</span>
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por folio, título o solicitante…" class="form-control pl-10">
            </div>
            <button type="submit" class="btn-secondary">Buscar</button>
        </form>

        <div class="flex items-center gap-1 overflow-x-auto no-scrollbar pb-1">
            <a href="{{ route('tickets.index', array_filter(['q' => request('q')])) }}"
               @class([
                   'px-3 py-1 rounded-lg whitespace-nowrap transition-colors',
                   'bg-primary-container text-on-primary-container font-semibold' => ! request('estado'),
                   'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' => request('estado'),
               ])>Todos</a>
            @foreach ($estados as $estado)
                @php $activo = request('estado') === $estado->code; @endphp
                <a href="{{ route('tickets.index', array_filter(['estado' => $estado->code, 'q' => request('q')])) }}"
                   @class([
                       'inline-flex items-center gap-1.5 px-3 py-1 rounded-lg whitespace-nowrap transition-colors',
                       'bg-primary-container text-on-primary-container font-semibold' => $activo,
                       'text-on-surface-variant hover:bg-surface-container hover:text-on-surface' => ! $activo,
                   ])>
                    <span class="w-1.5 h-1.5 rounded-full" style="background: {{ config('dashboard.status_colors')[$estado->code] ?? '#9ca3af' }}"></span>
                    {{ $estado->name }}
                </a>
            @endforeach
        </div>
    </div>

    <div class="dash-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="dash-table">
                <thead>
                    <tr>
                        <th>Folio</th>
                        <th>Título</th>
                        <th>Solicitante</th>
                        <th>Área</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th class="text-right">SLA</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        @php
                            $restante = \App\Support\SlaReloj::restante($ticket);
                            $slaTone = \App\Support\SlaReloj::tono($ticket);
                        @endphp
                        <tr class="group">
                            <td class="font-code text-xs text-on-surface-variant whitespace-nowrap">{{ $ticket->ticket_number }}</td>
                            <td class="max-w-xs">
                                <a href="{{ route('tickets.show', $ticket) }}" class="font-semibold hover:text-primary transition-colors line-clamp-1">{{ $ticket->title }}</a>
                                <span class="font-code text-[11px] text-outline">{{ $ticket->created_at->locale('es')->diffForHumans() }}</span>
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <x-dash.avatar :name="$ticket->requester->name" class="w-7 h-7" />
                                    <span class="truncate">{{ $ticket->requester->name }}</span>
                                </div>
                            </td>
                            <td class="text-on-surface-variant">{{ $ticket->assignedDepartment->name }}</td>
                            <td><x-dash.priority-badge :priority="$ticket->priority" /></td>
                            <td><x-dash.status-badge :status="$ticket->status" /></td>
                            <td class="text-right whitespace-nowrap">
                                @if ($restante)
                                    <span class="font-code text-xs font-semibold {{ config("dashboard.tones.$slaTone.text") }}">{{ $restante }}</span>
                                @else
                                    <span class="text-xs text-outline">—</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('tickets.show', $ticket) }}" class="btn-ghost btn-sm opacity-70 group-hover:opacity-100">
                                    Ver <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center">
                                <span class="material-symbols-outlined text-[40px] text-outline">inbox</span>
                                <p class="mt-2 text-on-surface-variant">No hay tickets para mostrar.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($tickets->hasPages())
            <div class="px-4 py-3 bg-surface-container">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
