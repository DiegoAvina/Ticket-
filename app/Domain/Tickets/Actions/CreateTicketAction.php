<?php

namespace App\Domain\Tickets\Actions;

use App\Domain\Assets\Models\Asset;
use App\Domain\Catalog\Models\Service;
use App\Domain\SLA\Models\SlaPolicy;
use App\Domain\Tickets\Enums\TicketStatusCode;
use App\Domain\Tickets\Events\TicketCreated;
use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Models\TicketHistory;
use App\Domain\Tickets\Models\TicketStatus;
use App\Domain\Tickets\Services\GenerateTicketNumber;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateTicketAction
{
    public function __construct(
        private readonly GenerateTicketNumber $generarFolio,
    ) {}

    /**
     * @param  array{
     *     service_id?: int|null,
     *     assigned_department_id?: int|null,
     *     category_id?: int|null,
     *     asset_id?: int|null,
     *     priority_id?: int|null,
     *     title: string,
     *     description: string,
     *     source?: string,
     *     external_ref?: string|null,
     * }  $datos
     */
    public function ejecutar(User $requester, array $datos): Ticket
    {
        $service = isset($datos['service_id']) ? Service::find($datos['service_id']) : null;

        $assignedDepartmentId = $service?->department_id ?? $datos['assigned_department_id'] ?? null;

        if (! $assignedDepartmentId) {
            throw ValidationException::withMessages([
                'assigned_department_id' => 'No fue posible determinar el área responsable del ticket.',
            ]);
        }

        $priorityId = $datos['priority_id'] ?? $service?->default_priority_id;

        if (! $priorityId) {
            throw ValidationException::withMessages([
                'priority_id' => 'Debes indicar una prioridad.',
            ]);
        }

        $slaPolicy = $service?->slaPolicy
            ?? SlaPolicy::where('priority_id', $priorityId)->first();

        $assetId = null;

        if (! empty($datos['asset_id'])) {
            $asset = Asset::where('id', $datos['asset_id'])
                ->where('assigned_user_id', $requester->id)
                ->first();

            if (! $asset) {
                throw ValidationException::withMessages([
                    'asset_id' => 'El equipo seleccionado no está asignado a tu usuario.',
                ]);
            }

            $assetId = $asset->id;
        }

        return DB::transaction(function () use ($requester, $datos, $service, $assignedDepartmentId, $priorityId, $slaPolicy, $assetId) {
            $estadoNuevo = TicketStatus::where('code', TicketStatusCode::NEW)->firstOrFail();

            $ahora = now();

            $ticket = Ticket::create([
                'ticket_number' => $this->generarFolio->generar((int) $ahora->year),
                'requester_id' => $requester->id,
                'requester_department_id' => $requester->department_id,
                'assigned_department_id' => $assignedDepartmentId,
                'service_id' => $service?->id,
                'category_id' => $datos['category_id'] ?? $service?->category_id,
                'asset_id' => $assetId,
                'priority_id' => $priorityId,
                'status_id' => $estadoNuevo->id,
                'title' => $datos['title'],
                'description' => $datos['description'],
                'source' => $datos['source'] ?? 'web',
                'external_ref' => $datos['external_ref'] ?? null,
                'sla_policy_id' => $slaPolicy?->id,
                'sla_response_due_at' => $slaPolicy ? $ahora->clone()->addMinutes($slaPolicy->first_response_minutes) : null,
                'sla_resolution_due_at' => $slaPolicy ? $ahora->clone()->addMinutes($slaPolicy->resolution_minutes) : null,
            ]);

            TicketHistory::create([
                'ticket_id' => $ticket->id,
                'user_id' => $requester->id,
                'action' => 'creado',
                'old_value' => null,
                'new_value' => ['status' => TicketStatusCode::NEW],
                'metadata' => ['source' => $ticket->source],
            ]);

            TicketCreated::dispatch($ticket);

            return $ticket;
        });
    }
}
