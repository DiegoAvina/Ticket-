@props([
    'value' => null,
    'label' => '',
    'color' => null,
])

@php
    // Verde si cumple, ámbar si está cerca, rojo si está por debajo.
    $color ??= match (true) {
        $value === null => '#9ca3af',
        $value >= 90 => '#10b981',
        $value >= 75 => '#f59e0b',
        default => '#f43f5e',
    };
@endphp

<div {{ $attributes->merge(['class' => 'flex flex-col items-center']) }}>
    <div class="ring3d" style="--c: {{ $color }}; --p-final: {{ $value ?? 0 }}">
        <div class="ring3d__label">
            <span class="font-display text-3xl font-bold text-on-surface tabular-nums">
                @if ($value === null)
                    —
                @else
                    <span x-data="countUp({{ (float) $value }}, 1, 1600)" x-text="display">{{ $value }}</span><span class="text-lg">%</span>
                @endif
            </span>
            <span class="text-[11px] font-semibold uppercase tracking-wider text-outline">{{ $label }}</span>
        </div>
    </div>
</div>
