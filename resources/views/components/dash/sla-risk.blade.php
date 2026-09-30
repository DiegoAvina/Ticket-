{{-- Radar de tickets abiertos más próximos a vencer su SLA de resolución. --}}
@props(['tickets'])

@use('App\Support\SlaReloj')

@php
    $tones = config('dashboard.tones');
    $criticos = $tickets->filter(fn ($t) => SlaReloj::tono($t) === 'error')->count();
@endphp

<x-dash.panel title="SLA en riesgo" icon="emergency" tone="error">
    <x-slot:aside>
        @if ($criticos > 0)
            <span class="text-[11px] font-semibold uppercase tracking-wider bg-error/10 text-error px-2 py-0.5 rounded">{{ $criticos }} crítico(s)</span>
        @else
            <span class="text-[11px] font-semibold uppercase tracking-wider bg-tertiary-container/40 text-tertiary px-2 py-0.5 rounded">En control</span>
        @endif
    </x-slot:aside>

    @if ($tickets->isEmpty())
        <div class="flex flex-col items-center gap-1 py-6 text-center">
            <span class="material-symbols-outlined icon-fill text-[36px] text-tertiary">verified</span>
            <p class="text-on-surface-variant">No hay tickets abiertos con SLA por vencer.</p>
        </div>
    @else
        <div class="flex flex-col gap-2">
            @foreach ($tickets as $i => $ticket)
                @php $tono = SlaReloj::tono($ticket); @endphp
                <a href="{{ route('tickets.show', $ticket) }}" class="p-2 rounded-lg bg-surface-container hover:bg-surface-container-high transition-colors flex flex-col gap-1">
                    <div class="flex items-center justify-between gap-2">
                        <span class="font-code text-xs font-semibold {{ $tones[$tono]['text'] }}">{{ $ticket->ticket_number }}</span>
                        <span @class(['font-code text-xs px-2 py-0.5 rounded', $tones[$tono]['pill'], 'animate-pulse' => $tono === 'error'])>
                            {{ SlaReloj::restante($ticket) }}{{ str_starts_with(SlaReloj::restante($ticket), 'Vencido') ? '' : ' restante' }}
                        </span>
                    </div>
                    <p class="font-semibold text-on-surface truncate">{{ $ticket->title }}</p>
                    <div class="flex items-center justify-between gap-2 text-xs text-outline">
                        <span class="truncate">{{ $ticket->requester?->name }}</span>
                        @if ($ticket->assignedUser)
                            <span class="flex items-center gap-1 text-tertiary shrink-0">
                                <span class="material-symbols-outlined text-[14px]">support_agent</span>
                                {{ $ticket->assignedUser->name }}
                            </span>
                        @else
                            <span class="shrink-0">Sin asignar</span>
                        @endif
                    </div>
                    <div class="bar3d bar3d--thin mt-1">
                        <div class="bar3d__fill" style="--c: rgb(var(--{{ $tono === 'neutral' ? 'outline' : $tono }})); --d: {{ $i * 120 }}ms; width: {{ SlaReloj::progreso($ticket) }}%"></div>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <a href="{{ route('tickets.index') }}" class="w-full py-1.5 rounded-lg bg-surface-container-high hover:bg-surface-bright text-tertiary font-semibold transition-colors flex items-center justify-center gap-1">
        Ver todos los tickets
        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
    </a>
</x-dash.panel>
