<x-dashboard-layout title="Mi dashboard">
    @php $pctResueltos = $total > 0 ? round($resueltos / $total * 100) : 0; @endphp

    <x-dash.hero eyebrow="Portal de soporte" title="Mis solicitudes"
                 description="Consulta el avance de tus tickets, el tiempo restante de atención y crea nuevas solicitudes.">
        <x-dash.hero-button :href="route('tickets.index')" icon="list_alt">Ver mis tickets</x-dash.hero-button>
        <x-dash.hero-button :href="route('tickets.create')" icon="add_box" primary>Nueva solicitud</x-dash.hero-button>
    </x-dash.hero>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-dash.kpi eyebrow="Historial" title="Mis tickets" :value="$total" icon="confirmation_number" tone="secondary">
            <span>Creados por ti</span>
        </x-dash.kpi>

        <x-dash.kpi eyebrow="En cola" title="Abiertos" :value="$abiertos" icon="inbox" tone="primary">
            <span class="flex items-center gap-1.5">
                <span class="pulse-dot" style="--c: rgb(var(--primary))"></span> Esperando atención
            </span>
        </x-dash.kpi>

        <x-dash.kpi eyebrow="Atendiendo" title="En proceso" :value="$enProceso" icon="sync" tone="tertiary" color-value>
            <span>Un agente ya trabaja en ellos</span>
        </x-dash.kpi>

        <x-dash.kpi eyebrow="Completados" title="Resueltos" :value="$resueltos" icon="task_alt" tone="tertiary">
            <x-slot:badge>
                <span class="font-code text-xs text-outline">{{ $pctResueltos }}%</span>
            </x-slot:badge>
            <div class="w-full bar3d bar3d--thin">
                <div class="bar3d__fill" style="--c: rgb(var(--tertiary)); width: {{ $pctResueltos }}%"></div>
            </div>
        </x-dash.kpi>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <x-dash.ticket-stream :tickets="$misTickets" title="Mis solicitudes recientes" contexto="solicitante"
                              :ver-todos="route('tickets.index')" class="xl:col-span-8">
            <x-slot:empty>
                <a href="{{ route('tickets.create') }}" class="mt-2 inline-block font-medium text-primary hover:underline">Crear mi primera solicitud →</a>
            </x-slot:empty>
        </x-dash.ticket-stream>

        <div class="xl:col-span-4 flex flex-col gap-4">
            <x-dash.sla-risk :tickets="$enRiesgo" />
            <x-dash.donut :items="$porEstado" title="Mis tickets por estado" />
        </div>
    </div>
</x-dashboard-layout>
