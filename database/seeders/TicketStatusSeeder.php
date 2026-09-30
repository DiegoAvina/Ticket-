<?php

namespace Database\Seeders;

use App\Domain\Tickets\Enums\TicketStatusCode;
use App\Domain\Tickets\Models\TicketStatus;
use Illuminate\Database\Seeder;

class TicketStatusSeeder extends Seeder
{
    /**
     * Sección 6 del spec: flujo de estados inicial.
     */
    public function run(): void
    {
        $estados = [
            ['code' => TicketStatusCode::NEW, 'name' => 'Nuevo', 'order' => 1, 'is_closed' => false],
            ['code' => TicketStatusCode::ASSIGNED, 'name' => 'Asignado', 'order' => 2, 'is_closed' => false],
            ['code' => TicketStatusCode::IN_PROGRESS, 'name' => 'En proceso', 'order' => 3, 'is_closed' => false],
            ['code' => TicketStatusCode::WAITING_USER, 'name' => 'Esperando usuario', 'order' => 4, 'is_closed' => false],
            ['code' => TicketStatusCode::RESOLVED, 'name' => 'Resuelto', 'order' => 5, 'is_closed' => false],
            ['code' => TicketStatusCode::CLOSED, 'name' => 'Cerrado', 'order' => 6, 'is_closed' => true],
            ['code' => TicketStatusCode::CANCELLED, 'name' => 'Cancelado', 'order' => 7, 'is_closed' => true],
        ];

        foreach ($estados as $estado) {
            TicketStatus::firstOrCreate(['code' => $estado['code']], $estado);
        }
    }
}
