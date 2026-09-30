<div style="font-family:sans-serif;font-size:14px;color:#1f2937;">
    <p>Hola,</p>
    <p>El solicitante reabrió este ticket porque el problema continúa.</p>

    @if ($motivo)
    <blockquote style="margin:12px 0;padding:8px 12px;border-left:3px solid #d1d5db;color:#374151;">
        {{ $motivo }}
    </blockquote>
    @endif

    @include('emails.tickets._info')

    <p style="color:#6b7280;">{{ config('app.name') }} — Mesa de Ayuda</p>
</div>
