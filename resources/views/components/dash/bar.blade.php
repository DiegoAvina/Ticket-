@props([
    'label',
    'value' => 0,
    'max' => 0,
    'color' => '#6366f1',
    'delay' => 0,
    'caption' => null,
])

@php
    $pct = $max > 0 ? min(100, round(($value / $max) * 100, 1)) : 0;
@endphp

<div {{ $attributes }}>
    <div class="flex items-center justify-between mb-1.5 text-sm">
        <span class="flex items-center gap-2 min-w-0 font-medium text-on-surface">
            <span class="w-2 h-2 rounded-full shrink-0" style="background: {{ $color }}"></span>
            <span class="truncate">{{ $label }}</span>
        </span>
        <span class="shrink-0 font-code tabular-nums text-on-surface font-semibold">
            {{ $value }}
            <span class="ml-1 text-xs font-normal text-outline">{{ $caption ?? $pct.'%' }}</span>
        </span>
    </div>
    <div class="bar3d" role="progressbar" aria-valuenow="{{ $value }}" aria-valuemin="0" aria-valuemax="{{ $max }}" aria-label="{{ $label }}">
        <div class="bar3d__fill" @if ($pct == 0) data-empty @endif
             style="--c: {{ $color }}; --d: {{ $delay }}ms; width: {{ $pct }}%"></div>
    </div>
</div>
