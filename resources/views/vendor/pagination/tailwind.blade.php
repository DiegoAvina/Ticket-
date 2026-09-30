{{-- Paginación con los tokens de tema (reemplaza la vista por defecto de Laravel). --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginación" class="flex items-center justify-between gap-3 text-xs text-outline">
        <p class="hidden sm:block">
            Mostrando <span class="font-code font-semibold text-on-surface">{{ $paginator->firstItem() }}</span>
            – <span class="font-code font-semibold text-on-surface">{{ $paginator->lastItem() }}</span>
            de <span class="font-code font-semibold text-on-surface">{{ $paginator->total() }}</span>
        </p>

        <div class="flex items-center gap-1">
            @if ($paginator->onFirstPage())
                <span class="btn-secondary btn-sm opacity-40 cursor-not-allowed" aria-disabled="true">
                    <span class="material-symbols-outlined text-[16px]">chevron_left</span> Anterior
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-secondary btn-sm">
                    <span class="material-symbols-outlined text-[16px]">chevron_left</span> Anterior
                </a>
            @endif

            <span class="hidden md:flex items-center gap-1">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="px-2 font-code">{{ $element }}</span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="min-w-[2rem] px-2 py-1 rounded-lg text-center font-code font-semibold bg-primary-container text-on-primary-container">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="min-w-[2rem] px-2 py-1 rounded-lg text-center font-code text-on-surface-variant hover:bg-surface-container-high" aria-label="Ir a la página {{ $page }}">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-secondary btn-sm">
                    Siguiente <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </a>
            @else
                <span class="btn-secondary btn-sm opacity-40 cursor-not-allowed" aria-disabled="true">
                    Siguiente <span class="material-symbols-outlined text-[16px]">chevron_right</span>
                </span>
            @endif
        </div>
    </nav>
@endif
