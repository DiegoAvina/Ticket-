@props(['eyebrow' => 'Centro operativo activo', 'title', 'description' => null])

<section class="relative overflow-hidden rounded-xl bg-surface-container p-6 shadow-xl">
    <div class="absolute -right-24 -top-24 w-96 h-96 rounded-full bg-gradient-to-br from-primary-container/60 to-tertiary/20 blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center justify-center p-1.5 rounded-lg bg-tertiary-container text-tertiary">
                    <span class="material-symbols-outlined text-[18px]">bolt</span>
                </span>
                <span class="text-[11px] font-semibold uppercase tracking-wider text-tertiary">{{ $eyebrow }}</span>
                <span class="pulse-dot" style="--c: rgb(var(--tertiary))"></span>
            </div>
            <h1 class="font-display text-2xl sm:text-[32px] sm:leading-10 font-bold tracking-tight text-on-surface">{{ $title }}</h1>
            @if ($description)
                <p class="text-on-surface-variant max-w-2xl">{{ $description }}</p>
            @endif
        </div>

        @if ($slot->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2">{{ $slot }}</div>
        @endif
    </div>
</section>
