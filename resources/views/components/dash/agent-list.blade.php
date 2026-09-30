{{-- Lista de personas/áreas con su carga y una barra 3D. $items: [['name' => ..., 'detail' => ..., 'value' => int], ...] --}}
@props(['items', 'title' => 'Carga por agente', 'badge' => null, 'unidad' => 'activos'])

@php
    $max = max(1, collect($items)->max('value'));
    $series = config('dashboard.series_colors');
@endphp

<x-dash.panel :title="$title" icon="support_agent" tone="tertiary">
    @if ($badge)
        <x-slot:aside>
            <span class="font-code text-xs text-tertiary bg-tertiary-container/40 px-2 py-0.5 rounded">{{ $badge }}</span>
        </x-slot:aside>
    @endif

    @if (collect($items)->isEmpty())
        <p class="text-on-surface-variant py-2">No hay tickets asignados actualmente.</p>
    @else
        <div class="flex flex-col gap-1">
            @foreach ($items as $i => $item)
                <div class="flex flex-col gap-1.5 p-2 rounded-lg hover:bg-surface-container transition-colors">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2 min-w-0">
                            <x-dash.avatar :name="$item['name']" class="w-8 h-8" />
                            <div class="flex flex-col min-w-0">
                                <span class="font-semibold text-on-surface truncate">{{ $item['name'] }}</span>
                                @if (! empty($item['detail']))
                                    <span class="text-xs text-outline truncate">{{ $item['detail'] }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[11px] font-code bg-surface-container-high text-on-surface-variant shrink-0">{{ $item['value'] }} {{ $unidad }}</span>
                    </div>
                    <div class="bar3d">
                        <div class="bar3d__fill" @if ($item['value'] == 0) data-empty @endif
                             style="--c: {{ $series[$i % count($series)] }}; --d: {{ $i * 100 }}ms; width: {{ round($item['value'] / $max * 100, 1) }}%"></div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-dash.panel>
