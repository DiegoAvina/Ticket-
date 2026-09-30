<?php

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Enums\TicketStatusCode;
use App\Domain\Tickets\Events\TicketResolved;
use App\Domain\Tickets\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ResolveTicketAction
{
    public function __construct(
        private readonly ChangeTicketStatusAction $cambiarEstado,
        private readonly AddTicketMessageAction $agregarMensaje,
    ) {}

    public function ejecutar(Ticket $ticket, User $user, string $resolucion): Ticket
    {
        return DB::transaction(function () use ($ticket, $user, $resolucion) {
            $ticket = $this->cambiarEstado->ejecutar($ticket, TicketStatusCode::RESOLVED, $user, $resolucion);

            $this->agregarMensaje->ejecutar($ticket, $user, $resolucion);

            $ticket->refresh();

            TicketResolved::dispatch($ticket, $user);

            return $ticket;
        });
    }
}
