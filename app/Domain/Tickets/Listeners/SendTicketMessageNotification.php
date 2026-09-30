<?php

namespace App\Domain\Tickets\Listeners;

use App\Domain\Tickets\Events\TicketMessageAdded;
use App\Mail\Tickets\TicketMessageAddedMailable;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendTicketMessageNotification
{
    /**
     * Si comenta el solicitante, avisa al agente asignado (o a todo el área
     * si aún no hay agente). Si comenta el agente, avisa al solicitante.
     * Las notas internas nunca se le notifican al solicitante.
     */
    public function handle(TicketMessageAdded $event): void
    {
        $ticket = $event->ticket;
        $mensaje = $event->mensaje;

        $esRequester = $mensaje->user_id === $ticket->requester_id;

        if ($esRequester) {
            $destinatarios = $ticket->assigned_user_id
                ? User::where('id', $ticket->assigned_user_id)->get()
                : User::where('department_id', $ticket->assigned_department_id)->role(['agente', 'encargado'])->get();
        } elseif ($mensaje->type->value !== 'internal') {
            $destinatarios = collect([$ticket->requester]);
        } else {
            $destinatarios = collect();
        }

        foreach ($destinatarios as $destinatario) {
            Mail::to($destinatario->email)->send(new TicketMessageAddedMailable($ticket, $mensaje, $destinatario));
        }
    }
}
