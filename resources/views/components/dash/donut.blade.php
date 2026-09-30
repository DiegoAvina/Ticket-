{{-- Dona SVG animada. $items: [['label' => ..., 'value' => ..., 'color' => '#hex'], ...] --}}
@props(['items' => [], 'title' => 'Distribución por estado', 'icon' => 'pie_chart', 'centerLabel' => 'Total'])

@php
    $circunferencia = 251.33; // 2π·40
    $total = collect($items)->sum('value');
    $offset = 0;
    $segmentos = collect($items)->filter(fn ($i) => $i['value'] > 0)->values()->map(function ($item) use ($total, $circunferencia, &$offset) {
        $len = $total > 0 ? $item['value'] / $total * $circunferencia : 0;
        $seg = $item + ['len' => round($len, 2), 'offset' => round(-$offset, 2), 'pct' => $total > 0 ? round($item['value'] / $total * 100) : 0];
        $offset += $len;

        return $seg;
    });
@endphp

<x-dash.panel :title="$title" :icon="$icon" tone="secondary">
    <x-slot:aside>
        <span class="font-code text-xs text-outline">Actual</span>
    </x-slot:aside>

    @if ($total === 0)
        <p class="text-on-surface-variant py-4 text-center">Sin datos todavía.</p>
    @else
        <div class="flex flex-col sm:flex-row items-center justify-around gap-4 py-1">
            <div class="relative w-32 h-32 shrink-0 flex items-center justify-center">
                <svg class="w-full h-full -rotate-90" viewBox="0 0 100 100" aria-hidden="true">
                    <circle cx="50" cy="50" r="40" fill="transparent" stroke="rgb(var(--surface-container-highest))" stroke-width="12" />
                    @foreach ($segmentos as $i => $s)
                        <circle class="donut-seg" cx="50" cy="50" r="40" fill="transparent" stroke="{{ $s['color'] }}" stroke-width="12"
                                stroke-dashoffset="{{ $s['offset'] }}" style="--len: {{ $s['len'] }}; --d: {{ $i * 120 }}ms">
                            <title>{{ $s['label'] }}: {{ $s['value'] }}</title>
                        </circle>
                    @endforeach
                </svg>
                <div class="absolute flex flex-col items-center">
                    <span class="font-display text-xl font-bold text-on-surface tabular-nums" x-data="countUp({{ $total }})" x-text="display">{{ $total }}</span>
                    <span class="text-[11px] font-semibold uppercase text-outline">{{ $centerLabel }}</span>
                </div>
            </div>

            <ul class="flex flex-col gap-1 w-full sm:w-auto">
                @foreach ($segmentos as $s)
                    <li class="flex items-center justify-between gap-4 text-xs">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-sm" style="background: {{ $s['color'] }}"></span>
                            <span class="text-on-surface font-medium">{{ $s['label'] }}</span>
                        </span>
                        <span class="font-code font-semibold text-outline">{{ $s['pct'] }}% ({{ $s['value'] }})</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ $slot }}
</x-dash.panel>
