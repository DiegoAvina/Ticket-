<?php

namespace App\Domain\Tickets\Listeners;

use App\Domain\Tickets\Enums\TicketStatusCode;
use App\Domain\Tickets\Events\TicketStatusChanged;
use App\Mail\Tickets\TicketWaitingUserMailable;
use Illuminate\Support\Facades\Mail;

class SendTicketStatusChangedNotification
{
    /**
     * De todas las transiciones de estado, solo "esperando usuario" requiere
     * avisarle al solicitante por correo (necesita actuar). El resto de
     * cambios de estado son internos o ya tienen su propia notificación
     * específica (resuelto, cerrado, reabierto).
     */
    public function handle(TicketStatusChanged $event): void
    {
        if ($event->estadoNuevo !== TicketStatusCode::WAITING_USER) {
            return;
        }

        Mail::to($event->ticket->requester->email)->send(new TicketWaitingUserMailable($event->ticket));
    }
}
