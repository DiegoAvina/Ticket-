@props(['status'])

@php
    $code = $status?->code;
    $tone = config('dashboard.status_tones')[$code] ?? 'neutral';
    $activo = in_array($code, ['in_progress', 'new'], true);
@endphp

<span class="inline-flex items-center gap-1.5 text-xs whitespace-nowrap {{ config("dashboard.tones.$tone.text") }}">
    @if ($activo)
        <span class="pulse-dot w-1.5 h-1.5" style="--c: currentColor"></span>
    @else
        <span class="w-1.5 h-1.5 rounded-full {{ config("dashboard.tones.$tone.solid") }}"></span>
    @endif
    <span class="truncate">{{ $status?->name }}</span>
</span>
