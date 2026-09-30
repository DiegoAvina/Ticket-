{{--
    Tarjeta de indicador. Slots opcionales:
    - $badge: píldora junto al valor (p. ej. "Meta 95%").
    - $slot: pie de la tarjeta.
--}}
@props([
    'eyebrow',
    'title',
    'value' => null,
    'decimals' => 0,
    'suffix' => '',
    'icon' => 'insights',
    'tone' => 'primary',
    'colorValue' => false,
])

@php $t = config("dashboard.tones.$tone"); @endphp

<div {{ $attributes->merge(['class' => 'dash-card group p-4 flex flex-col justify-between overflow-hidden hover:bg-surface-container']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="flex flex-col min-w-0">
            <span class="text-[11px] font-semibold uppercase tracking-wider text-outline">{{ $eyebrow }}</span>
            <span class="font-display font-semibold text-base text-on-surface mt-1 truncate">{{ $title }}</span>
        </div>
        <span class="p-1.5 rounded-lg {{ $t['chip'] }} transition-transform group-hover:scale-110">
            <span class="material-symbols-outlined text-[22px]">{{ $icon }}</span>
        </span>
    </div>

    <div class="mt-4 flex items-baseline justify-between gap-2">
        <span class="font-display text-[40px] leading-[48px] font-bold tracking-tight tabular-nums {{ $colorValue ? $t['text'] : 'text-on-surface' }}">
            @if ($value === null)
                —
            @else
                <span x-data="countUp({{ (float) $value }}, {{ (int) $decimals }})" x-text="display">{{ $value }}</span>{{ $suffix }}
            @endif
        </span>
        {{ $badge ?? '' }}
    </div>

    @if ($slot->isNotEmpty())
        <div class="mt-2 pt-1 flex items-center justify-between gap-2 text-xs text-outline">{{ $slot }}</div>
    @endif
</div>
