<?php

namespace App\Domain\Tickets\Actions;

use App\Domain\Tickets\Events\TicketSlaBreached;
use App\Domain\Tickets\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class CheckTicketSlaAction
{
    /**
     * Ventana de aviso: cuántos minutos antes del vencimiento se manda el
     * aviso de "próximo a vencer".
     */
    private const MINUTOS_AVISO_PREVIO = 120;

    private const ESTADOS_CERRADOS = ['resolved', 'closed', 'cancelled'];

    /**
     * Revisa los tickets abiertos y dispara TicketSlaBreached para los que
     * están por vencer o ya vencieron su SLA de resolución, marcando cada
     * uno para no volver a notificar lo mismo en la siguiente corrida.
     *
     * @return array{proximos_a_vencer: int, vencidos: int}
     */
    public function ejecutar(?Carbon $ahora = null): array
    {
        $ahora ??= now();

        $baseAbiertos = fn (): Builder => Ticket::query()
            ->whereNotNull('sla_resolution_due_at')
            ->whereDoesntHave('status', fn (Builder $q) => $q->whereIn('code', self::ESTADOS_CERRADOS));

        $proximos = (clone $baseAbiertos())
            ->whereNull('sla_resolution_warned_at')
            ->whereNull('sla_resolution_breached_at')
            ->where('sla_resolution_due_at', '>', $ahora)
            ->where('sla_resolution_due_at', '<=', $ahora->clone()->addMinutes(self::MINUTOS_AVISO_PREVIO))
            ->get();

        foreach ($proximos as $ticket) {
            $ticket->update(['sla_resolution_warned_at' => $ahora]);
            TicketSlaBreached::dispatch($ticket, 'proximo_a_vencer');
        }

        $vencidos = (clone $baseAbiertos())
            ->whereNull('sla_resolution_breached_at')
            ->where('sla_resolution_due_at', '<=', $ahora)
            ->get();

        foreach ($vencidos as $ticket) {
            $ticket->update(['sla_resolution_breached_at' => $ahora]);
            TicketSlaBreached::dispatch($ticket, 'vencido');
        }

        return [
            'proximos_a_vencer' => $proximos->count(),
            'vencidos' => $vencidos->count(),
        ];
    }
}
