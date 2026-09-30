<x-dashboard-layout title="Dashboard de agente">
    @php
        $colores = config('dashboard.status_colors');
        $cargaActiva = $enProceso + $esperandoUsuario;
    @endphp

    <x-dash.hero eyebrow="Mesa de trabajo" title="Mis tickets asignados"
                 description="Tu carga activa, los tickets nuevos de tu área y lo que está cerca de vencer su SLA.">
        <x-dash.hero-button :href="route('tickets.index')" icon="list_alt">Todos los tickets</x-dash.hero-button>
        <x-dash.hero-button :href="route('tickets.index', ['estado' => 'new'])" icon="move_to_inbox" primary>
            Tomar nuevos
            @if ($nuevosDelArea > 0)
                <span class="px-1.5 py-0.5 rounded text-[10px] font-code bg-surface text-primary">{{ $nuevosDelArea }}</span>
            @endif
        </x-dash.hero-button>
    </x-dash.hero>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-dash.kpi eyebrow="Mi área" title="Nuevos sin asignar" :value="$nuevosDelArea" icon="inbox" tone="secondary">
            @if ($nuevosDelArea > 0)
                <span class="flex items-center gap-1.5 text-secondary font-medium">
                    <span class="pulse-dot" style="--c: rgb(var(--secondary))"></span> Esperando a un agente
                </span>
            @else
                <span>Cola al día</span>
            @endif
        </x-dash.kpi>

        <x-dash.kpi eyebrow="Trabajando" title="En proceso" :value="$enProceso" icon="sync" tone="tertiary" color-value>
            <span>Asignados a ti</span>
        </x-dash.kpi>

        <x-dash.kpi eyebrow="Pausados" title="Esperando usuario" :value="$esperandoUsuario" icon="hourglass_top" tone="primary">
            <span>Pendientes de respuesta del solicitante</span>
        </x-dash.kpi>

        <x-dash.kpi eyebrow="Urgente" title="Próximos a vencer" :value="$proximosAVencer" icon="alarm" tone="error" color-value>
            <span class="flex items-center gap-1.5">
                @if ($proximosAVencer > 0)
                    <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span>
                @endif
                SLA en las próximas 4 h
            </span>
        </x-dash.kpi>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <x-dash.ticket-stream :tickets="$misTickets" title="Mis tickets asignados" :ver-todos="route('tickets.index')" class="xl:col-span-8" />

        <div class="xl:col-span-4 flex flex-col gap-4">
            <x-dash.sla-risk :tickets="$enRiesgo" />

            <x-dash.panel title="Mi carga activa" icon="stacked_bar_chart" tone="tertiary">
                <x-slot:aside>
                    <span class="font-code text-xs text-outline">{{ $cargaActiva }} activos</span>
                </x-slot:aside>
                <div class="flex flex-col gap-4">
                    <x-dash.bar label="En proceso" :value="$enProceso" :max="$cargaActiva" :color="$colores['in_progress']" />
                    <x-dash.bar label="Esperando usuario" :value="$esperandoUsuario" :max="$cargaActiva" :color="$colores['waiting_user']" delay="150" />
                    <x-dash.bar label="Próximos a vencer" :value="$proximosAVencer" :max="$cargaActiva" color="#f43f5e" delay="300" />
                </div>
            </x-dash.panel>

            <x-dash.donut :items="$porEstado" title="Mis tickets por estado" />
        </div>
    </div>
</x-dashboard-layout>
