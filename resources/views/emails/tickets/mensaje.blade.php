<div style="font-family:sans-serif;font-size:14px;color:#1f2937;">
    <p>Hola {{ $destinatario->name }},</p>
    <p><strong>{{ $mensaje->user?->name ?? 'Alguien' }}</strong> comentó en el ticket:</p>

    <blockquote style="margin:12px 0;padding:8px 12px;border-left:3px solid #d1d5db;color:#374151;">
        {{ $mensaje->body }}
    </blockquote>

    @include('emails.tickets._info')

    <p style="color:#6b7280;">{{ config('app.name') }} — Mesa de Ayuda</p>
</div>
