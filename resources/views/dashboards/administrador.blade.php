<x-dashboard-layout title="Dashboard global">
    @php
        $areas = $porArea->map(fn ($d) => [
            'name' => $d->name,
            'detail' => $total > 0 ? round($d->tickets_count / $total * 100).'% del total' : null,
            'value' => (int) $d->tickets_count,
        ]);
        $abiertos = $porEstado->whereNotIn('code', ['resolved', 'closed', 'cancelled'])->sum('value');
    @endphp

    <x-dash.hero title="Centro de comando de soporte"
                 description="Monitoreo en tiempo real de todos los tickets, cumplimiento de SLA y carga por área.">
        <x-dash.hero-button :href="route('admin.users.index')" icon="group">Usuarios</x-dash.hero-button>
        <x-dash.hero-button :href="route('tickets.index')" icon="list_alt">Todos los tickets</x-dash.hero-button>
        <x-dash.hero-button :href="route('tickets.create')" icon="add_box" primary>Crear ticket</x-dash.hero-button>
    </x-dash.hero>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <x-dash.kpi eyebrow="Volumen" title="Tickets totales" :value="$total" icon="inbox" tone="secondary">
            <span class="flex items-center gap-1.5">
                <span class="pulse-dot" style="--c: rgb(var(--tertiary))"></span> {{ $abiertos }} abiertos
            </span>
            <span class="font-code">{{ $porArea->count() }} áreas</span>
        </x-dash.kpi>

        <x-dash.kpi eyebrow="Riesgo" title="Vencidos" :value="$vencidos" icon="emergency" tone="error" color-value>
            <span>Fuera de SLA y sin cerrar</span>
            @if ($vencidos === 0)
                <span class="font-code text-tertiary font-semibold">Óptimo</span>
            @endif
        </x-dash.kpi>

        <x-dash.kpi eyebrow="Confiabilidad" title="Cumplimiento SLA" :value="$slaCumplimiento" decimals="1" suffix="%" icon="verified" tone="primary">
            <div class="w-full flex flex-col gap-1">
                <div class="bar3d bar3d--thin">
                    <div class="bar3d__fill" style="--c: rgb(var(--tertiary)); width: {{ $slaCumplimiento ?? 0 }}%"></div>
                </div>
                <span class="text-[11px] font-semibold uppercase">Resueltos antes del límite</span>
            </div>
        </x-dash.kpi>

        <x-dash.kpi eyebrow="Velocidad" title="Tiempo promedio" :value="$tiempoPromedioHoras" decimals="1" suffix=" h" icon="speed" tone="tertiary" color-value>
            <span>Creación → resolución</span>
        </x-dash.kpi>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-12 gap-6">
        <div class="xl:col-span-8 flex flex-col gap-4 min-w-0">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <x-dash.panel title="Tickets por estado" icon="view_in_ar" tone="primary">
                    @if ($porEstado->isEmpty())
                        <p class="text-on-surface-variant">Aún no hay tickets registrados.</p>
                    @else
                        <x-dash.columns :items="$porEstado->all()" />
                    @endif
                </x-dash.panel>

                <x-dash.panel title="Tendencia" subtitle="Tickets creados · últimos 30 días" icon="monitoring" tone="tertiary">
                    <div class="relative h-60">
                        <canvas id="tendenciaChart" aria-label="Tickets creados por día en los últimos 30 días"></canvas>
                    </div>
                </x-dash.panel>
            </div>

            <x-dash.ticket-stream :tickets="$ticketsRecientes" title="Flujo de tickets" :ver-todos="route('tickets.index')" />
        </div>

        <div class="xl:col-span-4 flex flex-col gap-4">
            <x-dash.sla-risk :tickets="$enRiesgo" />
            <x-dash.donut :items="$porEstado->all()" title="Distribución por estado" />
            <x-dash.agent-list :items="$areas" title="Tickets por área" unidad="tickets" />
        </div>
    </div>

    <x-slot:scripts>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            (() => {
                const canvas = document.getElementById('tendenciaChart');
                const token = (nombre, alfa = 1) => {
                    const rgb = getComputedStyle(document.documentElement).getPropertyValue(`--${nombre}`).trim().split(/\s+/).join(',');
                    return `rgba(${rgb}, ${alfa})`;
                };
                const degradado = () => {
                    const g = canvas.getContext('2d').createLinearGradient(0, 0, 0, 240);
                    g.addColorStop(0, token('tertiary', 0.35));
                    g.addColorStop(1, token('tertiary', 0));
                    return g;
                };

                const chart = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: @json($tendenciaFechas),
                        datasets: [{
                            label: 'Tickets creados',
                            data: @json($tendenciaTotales),
                            borderWidth: 2.5,
                            pointBorderWidth: 2,
                            pointRadius: 3,
                            pointHoverRadius: 6,
                            tension: 0.4,
                            fill: true,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: { duration: 1400, easing: 'easeOutQuart' },
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: { padding: 10, cornerRadius: 8, displayColors: false },
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { maxTicksLimit: 6 } },
                            y: { beginAtZero: true, border: { display: false }, ticks: { precision: 0 } },
                        },
                    },
                });

                // Recolorea la gráfica con los tokens del tema activo.
                const aplicarTema = () => {
                    const ds = chart.data.datasets[0];
                    ds.borderColor = token('tertiary');
                    ds.backgroundColor = degradado();
                    ds.pointBackgroundColor = token('surface-container-low');
                    ds.pointBorderColor = token('tertiary');
                    chart.options.scales.x.ticks.color = token('outline');
                    chart.options.scales.y.ticks.color = token('outline');
                    chart.options.scales.y.grid.color = token('outline-variant', 0.3);
                    chart.options.plugins.tooltip.backgroundColor = token('surface-container-highest');
                    chart.options.plugins.tooltip.titleColor = token('on-surface');
                    chart.options.plugins.tooltip.bodyColor = token('on-surface');
                    chart.update('none');
                };
                aplicarTema();
                window.addEventListener('theme-changed', aplicarTema);
            })();
        </script>
    </x-slot:scripts>
</x-dashboard-layout>
