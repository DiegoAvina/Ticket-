<div style="font-family:sans-serif;font-size:14px;color:#1f2937;">
    <p>Hola {{ $ticket->requester->name }},</p>
    <p>Tu ticket fue cerrado. Gracias por confirmar la solución.</p>

    @include('emails.tickets._info')

    <p style="color:#6b7280;">{{ config('app.name') }} — Mesa de Ayuda</p>
</div>
