<?php

namespace App\Domain\Tickets\Events;

use App\Domain\Tickets\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketEscalated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly User $user,
        public readonly ?string $motivo,
    ) {}
}
