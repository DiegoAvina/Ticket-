<?php

namespace App\Domain\Tickets\Listeners;

use App\Domain\Tickets\Events\TicketCreated;
use App\Mail\Tickets\TicketCreatedMailable;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendTicketCreatedNotification
{
    /**
     * Avisa a los agentes/encargados del área responsable que hay un
     * ticket nuevo sin asignar.
     */
    public function handle(TicketCreated $event): void
    {
        $destinatarios = User::where('department_id', $event->ticket->assigned_department_id)
            ->role(['agente', 'encargado'])
            ->get();

        foreach ($destinatarios as $destinatario) {
            Mail::to($destinatario->email)->send(new TicketCreatedMailable($event->ticket));
        }
    }
}
