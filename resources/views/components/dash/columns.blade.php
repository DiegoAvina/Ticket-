{{-- Gráfica de columnas isométricas 3D. $items: [['label' => ..., 'value' => ..., 'color' => '#hex'], ...] --}}
@props(['items' => []])

@php
    $max = max(1, collect($items)->max('value'));
@endphp

<div {{ $attributes->merge(['class' => 'col3d-chart']) }}>
    @foreach ($items as $i => $item)
        @php $h = round(($item['value'] / $max) * 78, 1); @endphp
        <div class="col3d" title="{{ $item['label'] }}: {{ $item['value'] }}">
            <div class="col3d__track">
                <div class="col3d__bar" style="--c: {{ $item['color'] }}; --h: {{ $h }}%; --d: {{ $i * 90 }}ms">
                    <span class="col3d__value tabular-nums">{{ $item['value'] }}</span>
                </div>
            </div>
            <div class="col3d__floor w-3/4 mt-1"></div>
            <p class="mt-2 text-[11px] leading-tight text-center text-on-surface-variant font-medium line-clamp-2">{{ $item['label'] }}</p>
        </div>
    @endforeach
</div>
