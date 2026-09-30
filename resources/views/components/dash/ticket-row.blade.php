@props(['ticket', 'contexto' => 'area'])

@use('App\Support\SlaReloj')

@php
    $finalizado = SlaReloj::finalizado($ticket);
    $restante = SlaReloj::restante($ticket);
    $slaTone = SlaReloj::tono($ticket);
    $prioTone = config('dashboard.priority_tones')[$ticket->priority?->level ?? 0] ?? 'neutral';
    // La franja lateral refleja la urgencia: prioridad crítica o SLA a punto de vencer.
    $franja = $finalizado ? 'tertiary' : ($slaTone === 'error' ? 'error' : $prioTone);
    $tones = config('dashboard.tones');

    [$principal, $secundario] = $contexto === 'solicitante'
        ? [$ticket->assignedDepartment?->name, $ticket->service?->name ?? $ticket->category?->name]
        : [$ticket->requester?->name, $ticket->requesterDepartment?->name ?? $ticket->assignedDepartment?->name];
@endphp

<div {{ $attributes->merge(['class' => 'relative grid grid-cols-1 md:grid-cols-12 gap-2 md:gap-2 px-4 py-4 items-center hover:bg-surface-container transition-colors group'.($finalizado ? ' opacity-75' : '')]) }}>
    <div class="absolute left-0 inset-y-0 w-1 {{ $tones[$franja]['solid'] }}"></div>

    {{-- Folio / asunto --}}
    <div class="md:col-span-5 flex flex-col gap-1 min-w-0 pr-2">
        <div class="flex items-center gap-2 flex-wrap">
            <span class="font-code text-xs font-semibold px-1.5 rounded {{ $tones[$franja]['pill'] }}">{{ $ticket->ticket_number }}</span>
            @if (($ticket->priority?->level ?? 0) >= 4 && ! $finalizado)
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-semibold uppercase bg-error/10 text-error">
                    <span class="pulse-dot w-1.5 h-1.5" style="--c: currentColor"></span> Crítico
                </span>
            @endif
            <span class="font-code text-[11px] text-outline">{{ $ticket->created_at->locale('es')->diffForHumans() }}</span>
        </div>
        <a href="{{ route('tickets.show', $ticket) }}"
           @class(['font-display font-semibold text-base text-on-surface hover:text-primary truncate transition-colors', 'line-through' => $finalizado])>
            {{ $ticket->title }}
        </a>
        @if ($ticket->category || $ticket->service)
            <div class="flex items-center gap-1.5 text-xs text-on-surface-variant truncate">
                <span class="material-symbols-outlined text-[14px] text-outline">layers</span>
                <span class="truncate">{{ collect([$ticket->service?->name, $ticket->category?->name])->filter()->join(' • ') }}</span>
            </div>
        @endif
    </div>

    {{-- Solicitante o área --}}
    <div class="md:col-span-3 flex items-center gap-2 min-w-0">
        <x-dash.avatar :name="$principal" shape="rounded-lg" class="w-8 h-8 text-sm" />
        <div class="flex flex-col min-w-0">
            <span class="font-medium text-on-surface truncate">{{ $principal ?? '—' }}</span>
            <span class="text-xs text-outline truncate">{{ $secundario ?? '—' }}</span>
        </div>
    </div>

    {{-- Prioridad y estado --}}
    <div class="md:col-span-2 flex md:flex-col gap-2 md:gap-1">
        <x-dash.priority-badge :priority="$ticket->priority" />
        <x-dash.status-badge :status="$ticket->status" />
    </div>

    {{-- SLA / responsable --}}
    <div class="md:col-span-2 flex items-center md:justify-end gap-2">
        <div class="flex flex-col md:items-end">
            @if ($finalizado)
                <span class="font-code text-xs font-medium text-tertiary flex items-center gap-1">
                    <span class="material-symbols-outlined text-[14px]">task_alt</span> Finalizado
                </span>
                @if ($ticket->resolved_at)
                    <span class="text-[11px] text-outline">Resolución: {{ SlaReloj::duracion((int) $ticket->created_at->diffInMinutes($ticket->resolved_at)) }}</span>
                @endif
            @elseif ($restante)
                <span class="font-code text-xs font-bold flex items-center gap-1 {{ $tones[$slaTone]['text'] }}">
                    <span class="material-symbols-outlined text-[14px]">{{ $slaTone === 'error' ? 'alarm' : 'schedule' }}</span>
                    {{ $restante }}
                </span>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-outline">SLA resolución</span>
            @else
                <span class="font-code text-xs text-outline">Sin SLA</span>
            @endif
        </div>
        <x-dash.avatar :name="$ticket->assignedUser?->name" class="w-8 h-8 ring-2 ring-surface" />
    </div>
</div>
