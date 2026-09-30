{{-- Avatar con iniciales; el color se elige de forma estable a partir del nombre. --}}
@props(['name' => null, 'shape' => 'rounded-full'])

@php
    $iniciales = collect(preg_split('/\s+/', trim($name ?? '')))
        ->filter()->take(2)->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))->join('');
    $tonos = [
        'bg-primary-container text-on-primary-container',
        'bg-secondary-container text-on-secondary-container',
        'bg-tertiary-container text-on-tertiary-container',
        'bg-surface-container-highest text-on-surface',
    ];
    $tono = $name ? $tonos[crc32($name) % count($tonos)] : 'bg-surface-container-high text-outline';
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center justify-center shrink-0 font-semibold text-[12px] $shape $tono"]) }} title="{{ $name }}">
    @if ($iniciales)
        {{ $iniciales }}
    @else
        <span class="material-symbols-outlined text-[16px]">person</span>
    @endif
</span>
