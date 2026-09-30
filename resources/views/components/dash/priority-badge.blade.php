@props(['priority'])

@php
    $nivel = $priority?->level ?? 0;
    $tone = config('dashboard.priority_tones')[$nivel] ?? 'neutral';
@endphp

<span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium w-fit whitespace-nowrap {{ config("dashboard.tones.$tone.pill") }}">
    @if ($nivel >= 4)
        <span class="material-symbols-outlined text-[13px] text-error">priority_high</span>
    @endif
    {{ $priority?->name ?? 'Sin prioridad' }}
</span>
