<?php

namespace App\Domain\Tickets\Events;

use App\Domain\Tickets\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Clase preparada para cuando exista el job programado que revise SLAs
 * vencidos (FASE 4: SLA/Notificaciones). Todavía no se dispara desde
 * ninguna Action.
 */
class TicketSlaBreached
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly string $tipo,
    ) {}
}
