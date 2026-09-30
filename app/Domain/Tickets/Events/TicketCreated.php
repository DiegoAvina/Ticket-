<?php

namespace App\Domain\Tickets\Events;

use App\Domain\Tickets\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
    ) {}
}
