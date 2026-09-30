{{-- Widget con encabezado (ícono + título) y un slot opcional $aside a la derecha. --}}
@props(['title', 'icon' => null, 'tone' => 'secondary', 'subtitle' => null])

@php $t = config("dashboard.tones.$tone"); @endphp

<section {{ $attributes->merge(['class' => 'dash-card p-4 flex flex-col gap-4']) }}>
    <div class="flex items-center justify-between gap-3">
        <div class="flex items-center gap-2 min-w-0">
            @if ($icon)
                <span class="p-1 rounded {{ $t['chip'] }}">
                    <span class="material-symbols-outlined text-[18px]">{{ $icon }}</span>
                </span>
            @endif
            <div class="min-w-0">
                <h3 class="font-display font-bold text-base text-on-surface truncate">{{ $title }}</h3>
                @if ($subtitle)
                    <p class="text-xs text-outline">{{ $subtitle }}</p>
                @endif
            </div>
        </div>
        {{ $aside ?? '' }}
    </div>
    {{ $slot }}
</section>
