<x-dashboard-layout :title="'Dashboard — '.$departamento->name">
    @php
        $activos = $nuevos + $enProceso + $esperandoUsuario;
        $agentes = $cargaPorAgente->sortByDesc('total')->values()->map(fn ($fila) => [
            'name' => $fila->assignedUser?->name ?? 'Sin nombre',
            'detail' => $fila->assignedUser?->email,
            'value' => (int) $fila->total,
        ]);
    @endphp

    <x-dash.hero :eyebrow="'Área · '.$departamento->name" title="Centro de control del área"
                 description="Desempeño del departamento, cumplimiento de SLA y distribución de la carga entre agentes.">
        <x-dash.hero-button :href="route('tickets.index')" icon="list_alt">Todos los tickets</x-dash.hero-button>
        <x-dash.hero-button :href="route('tickets.index', ['estado' => 'new'])" icon="assignment_ind" primary>Asignar nuevos</x-dash.hero-button>
    </x-dash.hero>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-dash.kpi eyebrow="Volumen activo" title="Tickets abiertos" :value="$activos" icon="inbox" tone="secondary">
            <span @class(['flex items-center gap-1.5 font-medium', 'text-error' => $vencidos > 0])>
                @if ($vencidos > 0)
                    <span class="w-2 h-2 rounded-full bg-error animate-pulse"></span>
                @endif
                {{ $vencidos }} vencido(s)
            </span>
            <span class="font-code">{{ $nuevos }} nuevos</span>
        </x-dash.kpi>

        <x-dash.kpi eyebrow="Velocidad" title="Tiempo promedio" :value="$tiempoPromedioHoras" decimals="1" suffix=" h" icon="speed" tone="tertiary" color-value>
            <span>Creación → resolución</span>
        </x-dash.kpi>

        <x-dash.kpi eyebrow="Confiabilidad" title="Cumplimiento SLA" :value="$slaCumplimiento" decimals="1" suffix="%" icon="verified" tone="primary">
            <div class="w-full flex flex-col gap-1">
                <div class="bar3d bar3d--thin">
                    <div class="bar3d__fill" style="--c: rgb(var(--tertiary)); width: {{ $slaCumplimiento ?? 0 }}%"></div>
                </div>
                <span class="text-[11px] font-semibold uppercase">Resueltos a tiempo</span>
            </div>
        </x-dash.kpi>

        <x-dash.kpi eyebrow="Completados" title="Resueltos" :value="$resueltos" icon="task_alt" tone="tertiary">
            <span>{{ $enProceso }} en proceso · {{ $esperandoUsuario }} en espera</span>
        </x-dash.kpi>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-8 flex flex-col gap-4 min-w-0">
            <x-dash.panel title="Tickets del área por estado" icon="view_in_ar" tone="primary">
                @if ($porEstado->isEmpty())
                    <p class="text-on-surface-variant">Aún no hay tickets en el área.</p>
                @else
                    <x-dash.columns :items="$porEstado->all()" />
                @endif
            </x-dash.panel>

            <x-dash.ticket-stream :tickets="$ticketsRecientes" title="Tickets recientes del área" :ver-todos="route('tickets.index')" />
        </div>

        <div class="xl:col-span-4 flex flex-col gap-4">
            <x-dash.sla-risk :tickets="$enRiesgo" />
            <x-dash.agent-list :items="$agentes" :badge="$agentes->count().' con carga'" />
        </div>
    </div>
</x-dashboard-layout>
