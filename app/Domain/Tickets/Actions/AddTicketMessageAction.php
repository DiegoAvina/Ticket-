<?php

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Enums\TicketMessageType;
use App\Domain\Tickets\Enums\TicketStatusCode;
use App\Domain\Tickets\Events\TicketMessageAdded;
use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Models\TicketHistory;
use App\Domain\Tickets\Models\TicketMessage;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddTicketMessageAction
{
    public function __construct(
        private readonly ChangeTicketStatusAction $cambiarEstado,
    ) {}

    public function ejecutar(Ticket $ticket, User $autor, string $cuerpo, TicketMessageType $tipo = TicketMessageType::Public): TicketMessage
    {
        return DB::transaction(function () use ($ticket, $autor, $cuerpo, $tipo) {
            $mensaje = TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => $autor->id,
                'type' => $tipo,
                'body' => $cuerpo,
            ]);

            $esRequester = $autor->id === $ticket->requester_id;

            if ($esRequester && $ticket->status->code === TicketStatusCode::WAITING_USER) {
                $ticket = $this->cambiarEstado->ejecutar($ticket, TicketStatusCode::IN_PROGRESS, $autor, 'Respuesta del solicitante.');
            } elseif (! $esRequester && $ticket->first_response_at === null) {
                $ticket->update(['first_response_at' => now()]);
            }

            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $autor->id,
                'action' => 'comentario_agregado',
                'old_value' => null,
                'new_value' => null,
                'metadata' => ['tipo' => $tipo->value, 'ticket_message_id' => $mensaje->id],
            ]);

            TicketMessageAdded::dispatch($ticket, $mensaje);

            return $mensaje;
        });
    }
}
