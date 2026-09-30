<?php

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Enums\TicketStatusCode;
use App\Domain\Tickets\Events\TicketReopened;
use App\Domain\Tickets\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class ReopenTicketAction
{
    public function __construct(
        private readonly ChangeTicketStatusAction $cambiarEstado,
    ) {}

    /**
     * Solo se puede reabrir un ticket que está "resuelto" pero cuyo
     * problema continúa (sección 2 del spec). Un ticket ya "cerrado"
     * queda fuera de este flujo en el MVP.
     */
    public function ejecutar(Ticket $ticket, User $user, string $motivo): Ticket
    {
        return DB::transaction(function () use ($ticket, $user, $motivo) {
            $ticket = $this->cambiarEstado->ejecutar($ticket, TicketStatusCode::IN_PROGRESS, $user, $motivo);

            TicketReopened::dispatch($ticket, $user, $motivo);

            return $ticket;
        });
    }
}
