<?php

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Events\TicketEscalated;
use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Models\TicketHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EscalateTicketAction
{
    public function ejecutar(Ticket $ticket, User $user, ?int $nuevoDepartamentoId, ?int $nuevaPrioridadId, ?string $motivo): Ticket
    {
        if (! $nuevoDepartamentoId && ! $nuevaPrioridadId) {
            throw ValidationException::withMessages([
                'escalamiento' => 'Indica un nuevo departamento y/o una nueva prioridad.',
            ]);
        }

        return DB::transaction(function () use ($ticket, $user, $nuevoDepartamentoId, $nuevaPrioridadId, $motivo) {
            $old = [
                'assigned_department_id' => $ticket->assigned_department_id,
                'priority_id' => $ticket->priority_id,
            ];

            $atributos = [];

            if ($nuevoDepartamentoId && $nuevoDepartamentoId !== $ticket->assigned_department_id) {
                $atributos['assigned_department_id'] = $nuevoDepartamentoId;
                // El agente asignado ya no aplica si el ticket cambia de área.
                $atributos['assigned_user_id'] = null;
            }

            if ($nuevaPrioridadId) {
                $atributos['priority_id'] = $nuevaPrioridadId;
            }

            $ticket->update($atributos);

            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'action' => 'escalado',
                'old_value' => $old,
                'new_value' => [
                    'assigned_department_id' => $ticket->assigned_department_id,
                    'priority_id' => $ticket->priority_id,
                ],
                'metadata' => $motivo ? ['motivo' => $motivo] : null,
            ]);

            $ticket->refresh();

            TicketEscalated::dispatch($ticket, $user, $motivo);

            return $ticket;
        });
    }
}
