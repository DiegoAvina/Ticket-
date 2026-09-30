<?php

namespace App\Console\Commands;

use App\Domain\Tickets\Actions\CheckTicketSlaAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('tickets:check-sla')]
#[Description('Revisa tickets abiertos próximos a vencer o vencidos su SLA de resolución y notifica por correo.')]
class CheckTicketSlaCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(CheckTicketSlaAction $accion): void
    {
        $resultado = $accion->ejecutar();

        $this->info("Próximos a vencer notificados: {$resultado['proximos_a_vencer']}");
        $this->info("Vencidos notificados: {$resultado['vencidos']}");
    }
}
