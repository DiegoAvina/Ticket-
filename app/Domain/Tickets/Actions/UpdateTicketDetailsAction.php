<?php

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Models\TicketHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateTicketDetailsAction
{
    /**
     * Edición administrativa de los datos base del ticket (no es parte del
     * ciclo de vida normal: no cambia estado, solo corrige título,
     * descripción, prioridad o categoría).
     *
     * @param  array{title?: string, description?: string, priority_id?: int, category_id?: int|null}  $datos
     */
    public function ejecutar(Ticket $ticket, User $user, array $datos): Ticket
    {
        return DB::transaction(function () use ($ticket, $user, $datos) {
            $anterior = $ticket->only(['title', 'description', 'priority_id', 'category_id']);

            $ticket->update($datos);

            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'action' => 'editado_por_admin',
                'old_value' => $anterior,
                'new_value' => $ticket->only(['title', 'description', 'priority_id', 'category_id']),
                'metadata' => null,
            ]);

            return $ticket->refresh();
        });
    }
}
