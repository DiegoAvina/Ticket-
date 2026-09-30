<div style="font-family:sans-serif;font-size:14px;color:#1f2937;">
    <p>Hola {{ $ticket->requester->name }},</p>
    <p>Tu ticket fue marcado como <strong>resuelto</strong>. Si el problema quedó solucionado, confírmalo desde el sistema para cerrarlo; si continúa, puedes reabrirlo.</p>

    @include('emails.tickets._info')

    <p style="color:#6b7280;">{{ config('app.name') }} — Mesa de Ayuda</p>
</div>
