<?php

namespace App\Domain\Tickets\Events;

use App\Domain\Tickets\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly string $estadoAnterior,
        public readonly string $estadoNuevo,
        public readonly User $user,
    ) {}
}
