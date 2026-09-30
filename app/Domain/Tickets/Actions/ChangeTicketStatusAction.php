<?php

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Enums\TicketStatusCode;
use App\Domain\Tickets\Events\TicketStatusChanged;
use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Models\TicketHistory;
use App\Domain\Tickets\Models\TicketStatus;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ChangeTicketStatusAction
{
    /**
     * Cambia el estado del ticket validando la matriz de transiciones de
     * TicketStatusCode. Es el único punto donde `status_id` cambia, para que
     * la validación de transiciones nunca se pueda saltar.
     */
    public function ejecutar(Ticket $ticket, string $nuevoEstadoCodigo, User $user, ?string $comentario = null): Ticket
    {
        $estadoAnterior = $ticket->status->code;

        if (! TicketStatusCode::puedeTransicionarA($estadoAnterior, $nuevoEstadoCodigo)) {
            throw ValidationException::withMessages([
                'estado' => "No es posible pasar de '{$estadoAnterior}' a '{$nuevoEstadoCodigo}'.",
            ]);
        }

        return DB::transaction(function () use ($ticket, $estadoAnterior, $nuevoEstadoCodigo, $user, $comentario) {
            $nuevoEstado = TicketStatus::where('code', $nuevoEstadoCodigo)->firstOrFail();

            $atributos = ['status_id' => $nuevoEstado->id];

            $atributos['resolved_at'] = $nuevoEstadoCodigo === TicketStatusCode::RESOLVED ? now() : $ticket->resolved_at;
            $atributos['closed_at'] = $nuevoEstadoCodigo === TicketStatusCode::CLOSED ? now() : $ticket->closed_at;

            if ($nuevoEstadoCodigo === TicketStatusCode::IN_PROGRESS && $estadoAnterior === TicketStatusCode::RESOLVED) {
                // Reapertura: el ticket vuelve a estar activo.
                $atributos['resolved_at'] = null;
            }

            $ticket->update($atributos);

            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $user->id,
                'action' => 'estado_cambiado',
                'old_value' => ['status' => $estadoAnterior],
                'new_value' => ['status' => $nuevoEstadoCodigo],
                'metadata' => $comentario ? ['comentario' => $comentario] : null,
            ]);

            $ticket->refresh();

            TicketStatusChanged::dispatch($ticket, $estadoAnterior, $nuevoEstadoCodigo, $user);

            return $ticket;
        });
    }
}
