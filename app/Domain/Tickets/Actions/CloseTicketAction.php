<?php

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Enums\TicketStatusCode;
use App\Domain\Tickets\Events\TicketClosed;
use App\Domain\Tickets\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CloseTicketAction
{
    public function __construct(
        private readonly ChangeTicketStatusAction $cambiarEstado,
    ) {}

    public function ejecutar(Ticket $ticket, User $user): Ticket
    {
        return DB::transaction(function () use ($ticket, $user) {
            $ticket = $this->cambiarEstado->ejecutar($ticket, TicketStatusCode::CLOSED, $user, 'Confirmado por el solicitante.');

            TicketClosed::dispatch($ticket, $user);

            return $ticket;
        });
    }
}
