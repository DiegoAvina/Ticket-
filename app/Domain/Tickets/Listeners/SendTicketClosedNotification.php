<?php

namespace App\Domain\Tickets\Listeners;

use App\Domain\Tickets\Events\TicketClosed;
use App\Mail\Tickets\TicketClosedMailable;
use Illuminate\Support\Facades\Mail;

class SendTicketClosedNotification
{
    public function handle(TicketClosed $event): void
    {
        Mail::to($event->ticket->requester->email)->send(new TicketClosedMailable($event->ticket));
    }
}
