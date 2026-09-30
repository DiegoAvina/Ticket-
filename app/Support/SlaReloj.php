<?php

namespace App\Support;

use App\Domain\Tickets\Models\Ticket;

/**
 * Cálculos de presentación del SLA de resolución de un ticket para el
 * dashboard: tiempo restante legible, % consumido y un "tono" de urgencia.
 */
final class SlaReloj
{
    private const FINALIZADOS = ['resolved', 'closed', 'cancelled'];

    public static function finalizado(Ticket $ticket): bool
    {
        return in_array($ticket->status?->code, self::FINALIZADOS, true);
    }

    /** "2h 15m", "3d 4h", "Vencido hace 40m" o null si no aplica. */
    public static function restante(Ticket $ticket): ?string
    {
        $limite = $ticket->sla_resolution_due_at;

        if (! $limite || self::finalizado($ticket)) {
            return null;
        }

        $minutos = (int) now()->diffInMinutes($limite, false);
        $texto = self::duracion(abs($minutos));

        return $minutos < 0 ? "Vencido hace {$texto}" : $texto;
    }

    /** Porcentaje del plazo de SLA ya consumido (0–100). */
    public static function progreso(Ticket $ticket): int
    {
        $limite = $ticket->sla_resolution_due_at;

        if (! $limite) {
            return 0;
        }

        $total = max(1, $ticket->created_at->diffInSeconds($limite));
        $usado = $ticket->created_at->diffInSeconds(now(), false);

        return (int) max(0, min(100, round($usado / $total * 100)));
    }

    /** error | secondary | tertiary | neutral según lo cerca que está de vencer. */
    public static function tono(Ticket $ticket): string
    {
        if (self::finalizado($ticket)) {
            return 'tertiary';
        }

        if (! $ticket->sla_resolution_due_at) {
            return 'neutral';
        }

        return match (true) {
            self::progreso($ticket) >= 85 => 'error',
            self::progreso($ticket) >= 60 => 'secondary',
            default => 'tertiary',
        };
    }

    public static function duracion(int $minutos): string
    {
        if ($minutos >= 1440) {
            return intdiv($minutos, 1440).'d '.intdiv($minutos % 1440, 60).'h';
        }

        return intdiv($minutos, 60).'h '.($minutos % 60).'m';
    }
}
