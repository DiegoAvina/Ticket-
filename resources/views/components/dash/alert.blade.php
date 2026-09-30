{{-- Aviso: success | error | warning | info. Con `dismissible` se puede cerrar. --}}
@props(['type' => 'info', 'dismissible' => false, 'title' => null])

@php
    [$clases, $icono] = match ($type) {
        'success' => ['bg-tertiary-container/40 text-on-tertiary-container dark:text-tertiary ring-tertiary/30', 'check_circle'],
        'error' => ['bg-error-container/50 text-on-error-container ring-error/30', 'error'],
        'warning' => ['bg-secondary-container/50 text-on-secondary-container ring-secondary/30', 'warning'],
        default => ['bg-primary-container/40 text-on-primary-container ring-primary/30', 'info'],
    };
@endphp

<div x-data="{ open: true }" x-show="open" x-transition.opacity role="{{ $type === 'error' ? 'alert' : 'status' }}"
     {{ $attributes->merge(['class' => "flex items-start gap-3 p-3 rounded-xl ring-1 ring-inset $clases"]) }}>
    <span class="material-symbols-outlined icon-fill text-[20px] shrink-0">{{ $icono }}</span>
    <div class="flex-1 min-w-0 text-sm">
        @if ($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        {{ $slot }}
    </div>
    @if ($dismissible)
        <button type="button" @click="open = false" class="shrink-0 opacity-70 hover:opacity-100" aria-label="Cerrar aviso">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
    @endif
</div>
