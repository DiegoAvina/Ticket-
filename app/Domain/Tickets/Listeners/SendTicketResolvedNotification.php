<?php

namespace App\Domain\Tickets\Listeners;

use App\Domain\Tickets\Events\TicketResolved;
use App\Mail\Tickets\TicketResolvedMailable;
use Illuminate\Support\Facades\Mail;

class SendTicketResolvedNotification
{
    public function handle(TicketResolved $event): void
    {
        Mail::to($event->ticket->requester->email)->send(new TicketResolvedMailable($event->ticket));
    }
}
