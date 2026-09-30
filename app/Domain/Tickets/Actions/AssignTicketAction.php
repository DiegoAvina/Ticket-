<?php

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Enums\TicketStatusCode;
use App\Domain\Tickets\Events\TicketAssigned;
use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Models\TicketAssignment;
use App\Domain\Tickets\Models\TicketHistory;
use App\Domain\Tickets\Models\TicketStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AssignTicketAction
{
    public function ejecutar(Ticket $ticket, User $agente, User $asignadoPor): Ticket
    {
        if ($agente->department_id !== $ticket->assigned_department_id) {
            throw ValidationException::withMessages([
                'agente' => 'El agente debe pertenecer al área responsable del ticket.',
            ]);
        }

        return DB::transaction(function () use ($ticket, $agente, $asignadoPor) {
            $agenteAnteriorId = $ticket->assigned_user_id;

            $ticket->assignments()
                ->whereNull('unassigned_at')
                ->update(['unassigned_at' => now()]);

            $atributos = ['assigned_user_id' => $agente->id];

            if ($ticket->status->code === TicketStatusCode::NEW) {
                $atributos['status_id'] = TicketStatus::where('code', TicketStatusCode::ASSIGNED)->value('id');
            }

            $ticket->update($atributos);

            TicketAssignment::create([
                'ticket_id' => $ticket->id,
                'user_id' => $agente->id,
                'assigned_by' => $asignadoPor->id,
                'assigned_at' => now(),
            ]);

            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $asignadoPor->id,
                'action' => 'asignado',
                'old_value' => ['assigned_user_id' => $agenteAnteriorId],
                'new_value' => ['assigned_user_id' => $agente->id],
                'metadata' => null,
            ]);

            $ticket->refresh();

            TicketAssigned::dispatch($ticket, $agente, $asignadoPor);

            return $ticket;
        });
    }
}
