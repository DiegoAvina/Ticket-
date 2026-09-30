<?php

namespace App\Domain\Tickets\Listeners;

use App\Domain\Tickets\Events\TicketSlaBreached;
use App\Mail\Tickets\TicketSlaMailable;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendTicketSlaNotification
{
    public function handle(TicketSlaBreached $event): void
    {
        $ticket = $event->ticket;

        $destinatarios = $ticket->assigned_user_id
            ? User::where('id', $ticket->assigned_user_id)->get()
            : User::where('department_id', $ticket->assigned_department_id)->role(['agente', 'encargado'])->get();

        foreach ($destinatarios as $destinatario) {
            Mail::to($destinatario->email)->send(new TicketSlaMailable($ticket, $event->tipo));
        }
    }
}
