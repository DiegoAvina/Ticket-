{{-- Bloque reutilizable con los datos clave del ticket, usado por todos los correos de notificación. --}}
<table style="width:100%;border-collapse:collapse;margin:16px 0;font-family:sans-serif;font-size:14px;">
    <tr>
        <td style="padding:4px 0;color:#6b7280;">Folio</td>
        <td style="padding:4px 0;font-weight:bold;">{{ $ticket->ticket_number }}</td>
    </tr>
    <tr>
        <td style="padding:4px 0;color:#6b7280;">Título</td>
        <td style="padding:4px 0;">{{ $ticket->title }}</td>
    </tr>
    <tr>
        <td style="padding:4px 0;color:#6b7280;">Área</td>
        <td style="padding:4px 0;">{{ $ticket->assignedDepartment->name }}</td>
    </tr>
    <tr>
        <td style="padding:4px 0;color:#6b7280;">Estado</td>
        <td style="padding:4px 0;">{{ $ticket->status->name }}</td>
    </tr>
</table>

<p style="font-family:sans-serif;font-size:14px;">
    <a href="{{ route('tickets.show', $ticket) }}" style="color:#4f46e5;">Ver el ticket completo →</a>
</p>
