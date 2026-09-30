<?php

namespace App\Domain\Tickets\Enums;

/**
 * Los estados viven en la tabla `ticket_statuses` (son "configurables" según
 * el spec), no en un enum nativo de PHP. Esta clase solo centraliza los
 * códigos sembrados por defecto y el mapa de transiciones válidas, para no
 * regar strings mágicos por las Actions.
 */
final class TicketStatusCode
{
    public const NEW = 'new';
    public const ASSIGNED = 'assigned';
    public const IN_PROGRESS = 'in_progress';
    public const WAITING_USER = 'waiting_user';
    public const RESOLVED = 'resolved';
    public const CLOSED = 'closed';
    public const CANCELLED = 'cancelled';

    /**
     * Estado actual => estados a los que se puede transicionar.
     *
     * @return array<string, array<string>>
     */
    public static function transitions(): array
    {
        return [
            self::NEW => [self::ASSIGNED, self::CANCELLED],
            self::ASSIGNED => [self::IN_PROGRESS, self::CANCELLED],
            self::IN_PROGRESS => [self::WAITING_USER, self::RESOLVED, self::CANCELLED],
            self::WAITING_USER => [self::IN_PROGRESS, self::CANCELLED],
            self::RESOLVED => [self::CLOSED, self::IN_PROGRESS],
            self::CLOSED => [],
            self::CANCELLED => [],
        ];
    }

    public static function puedeTransicionarA(string $actual, string $siguiente): bool
    {
        return in_array($siguiente, self::transitions()[$actual] ?? [], true);
    }
}
