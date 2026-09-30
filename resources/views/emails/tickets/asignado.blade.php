<div style="font-family:sans-serif;font-size:14px;color:#1f2937;">
    <p>Hola {{ $agente->name }},</p>
    <p>Se te asignó un ticket. Por favor revísalo cuando puedas.</p>

    @include('emails.tickets._info')

    <p style="color:#6b7280;">{{ config('app.name') }} — Mesa de Ayuda</p>
</div>
