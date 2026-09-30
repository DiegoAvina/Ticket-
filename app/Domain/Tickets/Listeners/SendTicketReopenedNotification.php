<?php

namespace App\Domain\Tickets\Listeners;

use App\Domain\Tickets\Events\TicketReopened;
use App\Mail\Tickets\TicketReopenedMailable;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendTicketReopenedNotification
{
    public function handle(TicketReopened $event): void
    {
        $ticket = $event->ticket;

        $destinatarios = $ticket->assigned_user_id
            ? User::where('id', $ticket->assigned_user_id)->get()
            : User::where('department_id', $ticket->assigned_department_id)->role(['agente', 'encargado'])->get();

        foreach ($destinatarios as $destinatario) {
            Mail::to($destinatario->email)->send(new TicketReopenedMailable($ticket, $event->motivo));
        }
    }
}
