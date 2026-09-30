<?php

namespace App\Domain\Tickets\Services;

use App\Domain\Tickets\Models\TicketCounter;

class GenerateTicketNumber
{
    /**
     * Genera el siguiente folio de ticket para el año dado.
     *
     * La transacción (y el lockForUpdate) deben ser controlados por el
     * proceso que está creando el ticket, igual que en
     * GenerarFolioAccionCorrectiva de sgiDasavena.
     */
    public function generar(int $anio): string
    {
        $contador = TicketCounter::query()
            ->where('year', $anio)
            ->lockForUpdate()
            ->first();

        if (! $contador) {
            $contador = TicketCounter::create([
                'year' => $anio,
                'last_number' => 0,
            ]);
        }

        $contador->increment('last_number');

        $numero = $contador->fresh()->last_number;

        return sprintf('TKT-%d-%06d', $anio, $numero);
    }
}
