{{--
    Encabezado de página. El slot va a la derecha (acciones).
    $back: URL opcional para el botón de regresar.
--}}
@props(['title', 'subtitle' => null, 'icon' => null, 'eyebrow' => null, 'back' => null])

<div class="relative overflow-hidden rounded-xl bg-surface-container p-5 shadow-md">
    <div class="absolute -right-20 -top-24 w-72 h-72 rounded-full bg-gradient-to-br from-primary-container/50 to-tertiary/10 blur-3xl pointer-events-none"></div>

    <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3 min-w-0">
            @if ($back)
                <a href="{{ $back }}" class="p-2 rounded-lg bg-surface-container-high hover:bg-surface-bright text-on-surface-variant transition-colors" aria-label="Regresar">
                    <span class="material-symbols-outlined text-[20px]">arrow_back</span>
                </a>
            @elseif ($icon)
                <span class="p-2 rounded-lg bg-primary-container/60 text-primary">
                    <span class="material-symbols-outlined text-[22px]">{{ $icon }}</span>
                </span>
            @endif
            <div class="min-w-0">
                @if ($eyebrow)
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-tertiary">{{ $eyebrow }}</p>
                @endif
                <h1 class="font-display text-2xl font-bold tracking-tight text-on-surface truncate">{{ $title }}</h1>
                @if ($subtitle)
                    <p class="text-on-surface-variant mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if ($slot->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
        @endif
    </div>
</div>
