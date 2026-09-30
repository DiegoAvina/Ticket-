<?php

namespace App\Domain\Tickets\Listeners;

use App\Domain\Tickets\Events\TicketAssigned;
use App\Mail\Tickets\TicketAssignedMailable;
use Illuminate\Support\Facades\Mail;

class SendTicketAssignedNotification
{
    public function handle(TicketAssigned $event): void
    {
        Mail::to($event->agente->email)->send(new TicketAssignedMailable($event->ticket, $event->agente));
    }
}
