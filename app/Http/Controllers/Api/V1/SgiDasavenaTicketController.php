<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Catalog\Models\Priority;
use App\Domain\Catalog\Models\Service;
use App\Domain\Tickets\Actions\CreateTicketAction;
use App\Domain\Tickets\Models\Ticket;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSgiDasavenaTicketRequest;
use App\Support\Users\FindOrCreateUserByEmail;
use Illuminate\Http\JsonResponse;

/**
 * Recibe los tickets que crea el chatbot de soporte de sgiDasavena y los da
 * de alta como tickets reales de TI, con la misma Action que usa el resto
 * del sistema. Es una ruta aparte de la API normal de tickets porque quien
 * llama no es el propio solicitante, sino un sistema externo autenticado con
 * un token de servicio (ver App\Console\Commands\CreateSgiDasavenaIntegrationToken).
 */
class SgiDasavenaTicketController extends Controller
{
    public function store(StoreSgiDasavenaTicketRequest $request, FindOrCreateUserByEmail $resolverUsuario, CreateTicketAction $accion): JsonResponse
    {
        $datos = $request->validated();

        $ticketExistente = Ticket::where('source', 'sgidasavena')
            ->where('external_ref', $datos['external_ref'])
            ->first();

        if ($ticketExistente) {
            return response()->json([
                'ticket_id' => $ticketExistente->id,
                'folio' => $ticketExistente->ticket_number,
            ]);
        }

        $requester = $resolverUsuario->resolver($datos['requester_email'], $datos['requester_name']);

        $servicio = Service::where('name', 'Chatbot SGI')
            ->whereHas('department', fn ($query) => $query->where('slug', 'ti'))
            ->firstOrFail();

        $descripcion = $datos['descripcion'];

        if (! empty($datos['requester_area'])) {
            $descripcion = "Área (sgiDasavena): {$datos['requester_area']}\n\n{$descripcion}";
        }

        $prioridadId = $datos['tipo'] === 'idea'
            ? Priority::where('name', 'Baja')->value('id')
            : null;

        $ticket = $accion->ejecutar($requester, [
            'service_id' => $servicio->id,
            'priority_id' => $prioridadId,
            'title' => $datos['asunto'],
            'description' => $descripcion,
            'source' => 'sgidasavena',
            'external_ref' => $datos['external_ref'],
        ]);

        return response()->json([
            'ticket_id' => $ticket->id,
            'folio' => $ticket->ticket_number,
        ], 201);
    }
}
