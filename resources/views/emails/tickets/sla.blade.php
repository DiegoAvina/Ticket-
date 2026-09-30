<div style="font-family:sans-serif;font-size:14px;color:#1f2937;">
    <p>Hola,</p>

    @if ($tipo === 'vencido')
        <p><strong style="color:#dc2626;">Este ticket ya pasó su fecha límite de resolución del SLA.</strong></p>
    @else
        <p><strong style="color:#d97706;">Este ticket está por vencer su SLA de resolución en las próximas horas.</strong></p>
    @endif

    @include('emails.tickets._info')

    <p style="color:#6b7280;">{{ config('app.name') }} — Mesa de Ayuda</p>
</div>
