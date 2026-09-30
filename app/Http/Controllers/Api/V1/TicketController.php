<?php

namespace App\Http\Controllers\Api\V1;

use App\Domain\Tickets\Actions\AddTicketMessageAction;
use App\Domain\Tickets\Actions\AssignTicketAction;
use App\Domain\Tickets\Actions\CloseTicketAction;
use App\Domain\Tickets\Actions\CreateTicketAction;
use App\Domain\Tickets\Actions\ReopenTicketAction;
use App\Domain\Tickets\Actions\ResolveTicketAction;
use App\Domain\Tickets\Models\Ticket;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Resources\TicketResource;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * API versionada (sección 27 del spec) — misma autorización y las mismas
 * Actions que la web (App\Http\Controllers\TicketController), para que un
 * futuro bot de Teams o app móvil respete el mismo aislamiento por
 * departamento que ya prueban los tests de la web.
 */
class TicketController extends Controller
{
    public function index(Request $request)
    {
        $tickets = Ticket::query()
            ->visibleTo($request->user())
            ->with(['requester', 'assignedDepartment', 'assignedUser', 'priority', 'status'])
            ->latest()
            ->paginate(20);

        return TicketResource::collection($tickets);
    }

    public function store(StoreTicketRequest $request, CreateTicketAction $accion)
    {
        $ticket = $accion->ejecutar($request->user(), $request->validated());

        return TicketResource::make($ticket)->response()->setStatusCode(201);
    }

    public function show(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['requester', 'assignedDepartment', 'assignedUser', 'priority', 'status']);

        return TicketResource::make($ticket);
    }

    public function addMessage(Request $request, Ticket $ticket, AddTicketMessageAction $accion)
    {
        $this->authorize('view', $ticket);

        $datos = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
        ]);

        $mensaje = $accion->ejecutar($ticket, $request->user(), $datos['body']);

        return response()->json(['id' => $mensaje->id, 'body' => $mensaje->body, 'created_at' => $mensaje->created_at], 201);
    }

    public function assign(Request $request, Ticket $ticket, AssignTicketAction $accion)
    {
        $this->authorize('update', $ticket);

        $datos = $request->validate([
            'agente_id' => ['required', 'exists:users,id'],
        ]);

        $accion->ejecutar($ticket, User::findOrFail($datos['agente_id']), $request->user());

        return TicketResource::make($ticket->fresh(['requester', 'assignedDepartment', 'assignedUser', 'priority', 'status']));
    }

    public function resolve(Request $request, Ticket $ticket, ResolveTicketAction $accion)
    {
        $this->authorize('update', $ticket);

        $datos = $request->validate([
            'resolucion' => ['required', 'string', 'max:5000'],
        ]);

        $accion->ejecutar($ticket, $request->user(), $datos['resolucion']);

        return TicketResource::make($ticket->fresh(['requester', 'assignedDepartment', 'assignedUser', 'priority', 'status']));
    }

    public function close(Request $request, Ticket $ticket, CloseTicketAction $accion)
    {
        $this->authorize('close', $ticket);

        $accion->ejecutar($ticket, $request->user());

        return TicketResource::make($ticket->fresh(['requester', 'assignedDepartment', 'assignedUser', 'priority', 'status']));
    }

    public function reopen(Request $request, Ticket $ticket, ReopenTicketAction $accion)
    {
        $this->authorize('reopen', $ticket);

        $datos = $request->validate([
            'motivo' => ['required', 'string', 'max:2000'],
        ]);

        $accion->ejecutar($ticket, $request->user(), $datos['motivo']);

        return TicketResource::make($ticket->fresh(['requester', 'assignedDepartment', 'assignedUser', 'priority', 'status']));
    }
}
